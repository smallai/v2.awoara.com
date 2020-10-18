<?php

namespace App\Http\Controllers\Admin;

use anerg\OAuth2\Driver\alipay;
use App\Models\Device;
use \App\Utils\IdGenerator;
use App\Jobs\WithdrawMoneyJob;
use App\Models\LogUserLogin;
use App\Models\Trade;
use App\Models\User;
use App\Models\WithdrawMoney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class WithdrawMoneyController extends Controller
{
    protected function items(Request $request)
    {
        $builder = WithdrawMoney::local()->with('device', 'owner');

        if (is_numeric($request->input('owner_id'))) {
            $builder->where('owner_id', $request->input('owner_id'));
        }

        $items = $builder->orderBy('created_at', 'desc')->paginate();

        if (is_numeric($request->input('owner_id'))) {
            $items->appends(['owner_id' => $request->input('owner_id')]);
        }

        return $items;
//        return WithdrawMoney::local()->with('owner', 'device')->latest()->paginate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = $this->items($request);
        $users = [];
        if (Auth::user()->isSuperAdmin()) {
            $users = User::all();
        }
        $owner_id = null;
        if (is_numeric($request->input('owner_id'))) {
            $owner_id = $request->input('owner_id');
        }
        return view('admin.withdraw.index', compact('items', 'users', 'owner_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        abort(403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = collect( $request->only('snap_uuid', 'device_id', 'owner_id', 'trades_id', 'withdraw_money') );
        $snap = Session::get(GetDeviceSnapKey($input['device_id']));
        debugbar()->debug($snap);
        if (($snap['snap_uuid'] == $input['snap_uuid'])
            && ($snap['withdraw_money'] == $input['withdraw_money']))
        {
            $input = $input->merge([
                'trades_id' => $snap['trades_id'],
                'client_ip' => $request->getClientIp(),
            ]);
            debugbar()->debug($input);

            WithdrawMoneyJob::dispatch($input);
            flash('正在处理,请稍候！！！')->info();
        }
        else
        {
            flash('页面已过期，请重试！')->warning();
        }

        return redirect()->route('dashboard.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = WithdrawMoney::local()->findOrFail($id);
        return view('admin.withdraw.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort(403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(403);
    }

    public function config()
    {
        return array(
            'app_id' =>  config('alipay.app_id'),
            'notify_url' => route('with'),
            'return_url' => route('payment.return_url'),
            'ali_public_key' => config('alipay.ali_public_key'),
            'private_key' => config('alipay.private_key'),
            'log' => array(
                'file' => storage_path('logs/alipay_withdraw.log'),
                'level' => 'debug'
            ),
            'mode' => 'dev',
        );
    }

    public function payment($withdraw)
    {
        $order = [
            'out_biz_no' => time(),
            'payee_type' => 'ALIPAY_LOGONID',
            'payee_account' => $withdraw->payee,
            'amount' => to_float($withdraw->withdraw),
            'payer_show_name' => $withdraw->show_name,
            'payee_real_name' => $withdraw->read_name,
            'remark' => $withdraw->remark,
        ];

        $alipay = Pay::alipay($this->config());
        $result = $alipay->transfer($order);
    }

    public function returnUrl(Request $request)
    {

    }

    public function notifyUrl(Request $request)
    {

    }

    // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
    // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
    // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
    // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
    // 4、验证app_id是否为该商户本身。
    // 5、其它业务逻辑情况
    protected function checkPaymentSuccess($data, $withdraw)
    {
        if ($data->out_trade_no != $withdraw->id)
        {
            \Log::debug('out_trade_no error', [$data, $withdraw]);
            return false;
        }

        if ($data->seller_id != config('alipay.seller_id'))
        {
            \Log::debug('seller_id error', [$data, $withdraw]);
            return false;
        }

        if ($data->seller_id != config('alipay.seller_id'))
        {
            \Log::debug('seller_id error', [$data, $withdraw]);
            return false;
        }

        if ($data->app_id != config('alipay.app_id'))
        {
            \Log::debug('app_id error', [$data, $withdraw]);
            return false;
        }

        if (0 != bccomp($data->total_amount, $withdraw->withdraw_money))
        {
            \Log::debug('total_amount error', [$data, $withdraw]);
            return false;
        }

        return true;
    }

    //删除提现失败的记录，并重置为未提现状态。
    public function reset(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            $id = $request->input('id', -1);
            Log::debug('id', ['id' => $id]);
            DB::connection()->enableQueryLog();
            DB::transaction(function () use ($id) {
                $item = WithdrawMoney::findOrFail($id);
                if ($item['withdraw_status'] !== Trade::WithdrawStatus_Success) {
                    Log::warning('reset withdraw record', ['item' => $item]);

                    Trade::where('withdraw_id', $item->id)->update([
                        'withdraw_id' => null,
                        'withdraw_status' => Trade::WithdrawStatus_None
                    ]);

                    $item->delete();
                }
            });

            $queries = DB::getQueryLog();
            DB::connection()->disableQueryLog();

            Log::debug('sqls', ['$queries' => $queries]);

            flash()->success('正在处理!');
            return redirect()->route('withdraw.index');
        }
        else {
            die(403);
        }
    }
}
