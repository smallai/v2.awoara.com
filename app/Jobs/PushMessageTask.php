<?php

namespace App\Jobs;

use App\Utils\IotDevice;
use App\Models\Trade;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class PushMessageTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 300;

    private $tradeId = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tradeId)
    {
        $this->tradeId = $tradeId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        for ($i=0; $i<10; $i++)
//        {
//            if ($this->process())
//            {
////                $this->delete();
//                return;
//            }
//            sleep(5);
//        }
    }

//    public function process()
//    {
//        $trade = Trade::findOrFail($this->tradeId);
//
//        if ($trade['confirm_status'] != Trade::GoodsStatus_Confirmed)   //订单已经推送成功了
//        {
//            Log::info("push order confirmed");
//            return true;
//        }
//
//        if (($trade['payment_status'] === Trade::PaymentStatus_Success) //已付款
//            && ($trade['refund_status'] === Trade::RefundStatus_None))  //未退款
//        {
//            if ($trade['confirm_status'] != Trade::GoodsStatus_Confirmed)   //推送失败的订单
//            {
//                //在２分钟之内
//                if (Carbon::now()->diffInSeconds($trade['payment_at']) <= 120) {
//                    //推送消息
//                    $iot = new IotDevice($trade['device_id']);
//                    if ($iot->netOrder($trade['id'], $trade['goods_seconds'])) {
//                        $trade['confirm_status'] = Trade::GoodsStatus_Confirmed;
//                        $trade['confirmed_at'] = Carbon::now();
//                        $trade->save();
//
//                        Log::info('push network order success', [
//                            'tradeId' => $trade['id']
//                        ]);
//                        return true;
//                    } else {
//                        //推送消息失败
//                        Log::warning('push network order failed', [
//                            'tradeId' => $trade['id']
//                        ]);
//                    }
//                } else {
//                    //订单已过期，用户可能已经离开了
//                    Log::warning('push order timeout', [
//                        'tradeId' => $trade['id'],
//                        'payment_at' => $trade['payment_at'],
//                        'now' => '' . Carbon::now()
//                    ]);
//                }
//            }
//        }
//
//        return false;
//    }
}
