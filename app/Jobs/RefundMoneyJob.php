<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Trade;
use Carbon\Carbon;
use EasyWeChat\Factory;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;

class RefundMoneyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->run();
    }

    public function run()
    {
        \Log::alert(''.Carbon::now());

        $this->delete();

        //这里只处理微信的退款记录即可。
        $trades = Trade::where('refund_status', Trade::RefundStatus_Processing)
            ->where('payment_type', Trade::PaymentType_WeChat)
            ->where('payment_status', Trade::PaymentStatus_Success)
            ->get();
        $wx_config = config('wechat.payment.default');
        $wx_config['notify_url'] = null;
        $app = Factory::payment($wx_config);

        foreach ($trades as $trade) {
            try {
                $result = $app->refund->queryByRefundId($trade['refund_id']);
                if (($result['return_code'] === 'SUCCESS') && ($result['result_code'] === 'SUCCESS')) { //通信成功,退款成功
                    if ($trade['refund_money'] === (integer)$result['refund_fee']) {
                        $trade['refund_status'] = Trade::RefundStatus_Success;
                        $trade->updateInfo();
                        $trade->save();
                    }
                } else {
                    Log::error('wechat refund return: ', $result);
                }
                if (array_key_exists('err_code_des', $result)) {
                    Log::error('err_code_des', $result['err_code_des']);
                }
            } catch (\Exception $exception) {
                Log::error('wechat refund', ['exception' => $exception]);
            }
        }
    }
}
