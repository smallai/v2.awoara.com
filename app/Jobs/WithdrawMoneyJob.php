<?php

namespace App\Jobs;

use App\Models\Device;
use \App\Utils\IdGenerator;
use App\Models\LogUserLogin;
use App\Models\Trade;
use App\Models\User;
use App\Models\WithdrawMoney;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;

class WithdrawMoneyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $param = null;

    protected $dateFormat = 'Y-m-d H:i:sO';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($param)
    {
//        \DB::enableQueryLog();
        $this->param = $param;
        Log::debug('job', [
            'job' => $param
        ]);
//        if (isset($param['trades_id']))
//            $this->param['trades_id'] = explode(',', $param['trades_id']);
    }

    public function __destruct()
    {
//        Log::debug(\DB::getQueryLog());
    }








    public function makeParam($device_id = 1)
    {
        $input = [];
        $device = Device::findOrFail($device_id);
        $trades = $device->trade()->canWithdrawMoney()->get();

        $input['device_id'] = $device->id;
        $input['owner_id'] = $device->owner_id;
        $input['trades_id'] = $trades->pluck('id');
        $input['client_ip'] = \Illuminate\Support\Facades\Request::getClientIp();
        $input['withdraw_money'] = $trades->sum('withdraw_money');

        return $input;
    }

//    public function test()
//    {
//        $input = $this->makeParam(1);
//        $this->param = $input;
//        debugbar()->debug($input);
//        $this->run();
//        return 'hello';
//    }

    public function handle()
    {
        $this->run();
    }

    public function run()
    {
        $input = $this->param;

        if (!$this->checkInput($input))
        {
            Log::warning('提现参数错误', $input);
            return;
        }
        Log::debug('提现参数检查通过');

        $device = Device::withTrashed()->findOrFail($input['device_id']);

        $owner = User::withTrashed()->findOrFail($input['owner_id']);
        if (!$this->checkOwner($owner))
        {
            Log::warning('提现设备拥有者信息错误', [
                \DB::getQueryLog(),
                $input,
                $owner,
            ]);
            return ;
        }

        $trades = $this->reloadTrades($owner, $device, $input['trades_id']);
        if (count($trades) < 1)
        {
            Log::warning('提现记录已过期', [
                \DB::getQueryLog(),
                $input,
                $trades,
            ]);
            return;
        }
        Log::debug('提现记录检查通过');

        $withdraw_money = $trades->sum('withdraw_money');
        if ($withdraw_money != $input['withdraw_money'])
        {
            Log::warning('提现金额错误', [
                \DB::getQueryLog(),
                $input,
                $trades,
            ]);
            return;
        }
        Log::debug('提现金额正确');
//        return;

//        try {
//            \DB::beginTransaction();

        $withdraw = $this->createWithdrawMoneyRecord($owner, $device, $trades);
        if ($this->checkWithdrawMoneyRecord($withdraw))
        {
            Log::debug('提现检查通过');
            if ($this->needWithdraw($withdraw))
            {
                Log::debug('需要提现'.$withdraw);
//                    $this->updateTradesStatus($trades, $withdraw, Trade::WithdrawStatus_Processing);
//
                $this->payment($withdraw, $trades);
            }
        }

//            \DB::commit();
//        }
//        catch (\Exception $exception)
//        {
//            \DB::rollBack();
//
//            Log::critical('操作异常', $input);
//            Log::critical($exception);
//        }

        $logs = \DB::getQueryLog();
        Log::debug($logs);
    }

    protected function checkInput($input)
    {
        if (!isset($input['owner_id']))
        {
            Log::debug('owner_id');
            return false;
        }
        if (!isset($input['device_id']))
        {
            Log::debug('device_id');
            return false;
        }
        if (!isset($input['trades_id']))
        {
            Log::debug('device_id');
            return false;
        }
        if (!isset($input['withdraw_money']))
        {
            Log::debug('withdraw_money');
            return false;
        }

        if ((integer)$input['owner_id'] <= 0)
        {
            Log::debug('owner_id <= 0');
            return false;
        }

        if ((integer)$input['device_id'] <= 0)
        {
            Log::debug('device_id <= 0');
            return false;
        }

        if (count($input['trades_id']) <= 0)
        {
            Log::debug('trades_id <= 0');
            return false;
        }

        if ((integer)$input['withdraw_money'] <= 0)
        {
            Log::debug('withdraw_money <= 0');
            return false;
        }

        return true;
    }

    protected function checkOwner($owner)
    {
        if (!isset($owner))
        {
            Log::debug('$owner');
            return false;
        }
        if (strlen($owner->payee) < 1)
        {
            Log::debug('payee < 1');
            return false;
        }
        if (strlen($owner->real_name) < 1)
        {
            Log::debug('real_name < 1');
            return false;
        }

        return true;
    }

    protected function reloadTrades($owner, $device, $trades_id)
    {
        return $device->trade()->canWithdrawMoney()->whereIn('id', $trades_id)->get();
    }

    //    创建提现记录
    protected function createWithdrawMoneyRecord($owner, $device, $trades)
    {
        Log::debug('begin create record');
        $logOwnerLastLogin = LogUserLogin::where('user_id', $owner->id)->latest()->first();
        $withdraw = new WithdrawMoney();
        $withdraw->id = IdGenerator::withdrawId();
        $withdraw->device_id = $device->id;
        $withdraw->owner_id = $owner->id;
        $withdraw->log_user_login_id = null;

        $withdraw->withdraw_status = Trade::WithdrawStatus_Processing;
//        $withdraw->withdraw_at = Carbon::now()->format($this->dateFormat);

        $withdraw->owner_ip = $this->param['client_ip'];
        $withdraw->owner_phone = $owner->phone;
        $withdraw->owner_email = $owner->email;
        $withdraw->owner_payee = $owner->payee;
        $withdraw->owner_real_name = $owner->real_name;

        $withdraw->payment_money = $trades->sum('payment_money');
        $withdraw->refund_money = $trades->sum('refund_money');
        $withdraw->withdraw_money = $trades->sum('withdraw_money');
        $withdraw->platform_money = $trades->sum('platform_money');

        $withdraw->payer_show_name = '萌芽洗车';
        $withdraw->payer_remark = ''.$withdraw->payer_show_name.':'.$device->name.'收益:'.to_float($withdraw->withdraw_money).'元';
        $withdraw->payment_type = Trade::PaymentType_Alipay;
        $withdraw->updateInfo();
        $withdraw->save();

        Log::debug('end create record');
        Log::debug('withdraw', [ $withdraw ]);
        return $withdraw;
    }

//https://docs.open.alipay.com/api_28/alipay.fund.trans.toaccount.transfer
//alipay.fund.trans.toaccount.transfer
//Collection {#2315 ▼
//    #items: array:5 [▼
//"code" => "10000"
//"msg" => "Success"
//"order_id" => "20180525110070001502160000124535"
//"out_biz_no" => "1527224153"
//"pay_date" => "2018-05-25 12:55:54"
    public function payment($withdraw, $trades)
    {
        $order = [
            'out_biz_no' => $withdraw->id,
            'payee_type' => 'ALIPAY_LOGONID',
            'payee_account' => $withdraw->owner_payee,
            'payee_real_name' => $withdraw->owner_real_name,
            'amount' => to_float($withdraw->withdraw_money),
            'payer_show_name' => $withdraw->payer_show_name,
            'remark' => $withdraw->payer_remark,
        ];

        try
        {
            Log::debug($order);
            $alipay = Pay::alipay($this->config());
            $result = $alipay->transfer($order);
            Log::debug($result);

            $withdraw->payment_trade_id = $result['order_id'];
            $withdraw->payment_code = $result['code'];
            $withdraw->payment_msg = $result['msg'];
//            $withdraw->payment_at = Carbon::now()->format($this->dateFormat);
            $withdraw->save();

            $isSuccess = false;
            if (isset($result['code']) && isset($result['msg']) && ($result['code'] === '10000') && ($result['msg'] === 'Success'))
                $isSuccess = true;
            $this->updateWithdrawStatus($trades, $withdraw, $isSuccess ? Trade::WithdrawStatus_Success : Trade::WithdrawStatus_Failed);
        }
        catch (\Exception $exception)
        {
            Log::error('exception', [$exception]);

            $this->updateWithdrawStatus($trades, $withdraw, Trade::WithdrawStatus_Failed);
            Log::emergency('提现任务提现错误', [
                'withdraw' => $withdraw,
                'trades' => $trades,
                'exception' => $exception,
            ]);
        }
    }

    //    检查提现记录是否正确
    protected function checkWithdrawMoneyRecord($withdraw)
    {
        $result = (0 === ($withdraw->payment_money - $withdraw->refund_money - $withdraw->withdraw_money - $withdraw->platform_money));
        Log::debug('checkWithdrawMoneyRecord', [
            $result
        ]);
        return $result;
    }

    //    更新提现状态
    protected function updateWithdrawStatus($trades, $withdraw, $status)
    {
        $result = false;

        try
        {
            \DB::beginTransaction();
            $withdraw->withdraw_status = $status;
            $withdraw->updateInfo();
            $withdraw->save();
            $result = Trade::whereIn('id', $trades->pluck('id'))->update([
                'withdraw_id' => $withdraw->id,
                'withdraw_status' => $withdraw->withdraw_status,
//                'withdraw_at' => Carbon::now()->format($this->dateFormat),
            ]);
            \DB::commit();
        }
        catch (\Exception $exception)
        {
            \DB::rollBack();
            Log::error('更新状态异常', [$exception]);
        }

        Log::debug('updateTradesStatus', [
            $result
        ]);

        return $result;
    }

    //    判断提现记录是否需要执行提现操作
    protected function needWithdraw($withdraw)
    {
        $result = ($withdraw->withdraw_money > 0);
        Log::debug('needWithdraw', [
            $result
        ]);
        return $result;
    }

    protected function config()
    {
        $conf = [
            'app_id' =>  config('alipay.app_id'),
//            'notify_url' => route('payment.alipay.notify'),
//            'return_url' => route('payment.alipay.return'),
            'ali_public_key' => config('alipay.ali_public_key'),
            'private_key' => config('alipay.private_key'),
            'log' => [
                'file' => storage_path('logs/alipay_withdraw.log'),
                'level' => 'debug'
            ],
//            'mode' => 'dev',
        ];

        Log::debug('alipay conf', ['config' => $conf]);
        return $conf;
    }
}
