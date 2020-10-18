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

class PushOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order_id = 0;

    public $tries = 30;

    public $interval = 6;

    public $release_delay = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::debug("begin push order", ['order_id' => $this->order_id, 'attempts' => $this->attempts()]);
            $this->pushOrder($this->order_id);
        } catch (\Exception $e) {
            if ($this->attempts() < $this->tries) {
                $this->release($this->release_delay);
            } else {
                $this->delete();
            }
        }
    }

    public function pushOrder($order_id)
    {
        $trade = Trade::findOrFail($order_id);
        if ($trade['payment_status'] === Trade::PaymentStatus_Success) {
            //没有推送消息
            if ($trade['confirm_status'] !== Trade::GoodsStatus_Confirmed) {
                //在２分钟之内
                $now = Carbon::now();
                $seconds = $now->diffInSeconds($trade['payment_at']);
                Log::debug('order delay', ['seconds' => $seconds, 'now' => $now, 'trade' => $trade]);
                if ($seconds <= 120) {
                    //推送消息
                    $iot = new IotDevice($trade['device_id']);
                    if ($iot->netOrder($trade['id'], $trade['goods_seconds'])) {
                        $trade['confirm_status'] = Trade::GoodsStatus_Confirmed;
                        $trade['confirmed_at'] = Carbon::now();
                        $trade->save();

                        Log::info('send network order success', [
                            'trade_id' => $trade['id']
                        ]);
                        $this->delete();
                    } else {
                        //推送消息失败
                        Log::warning('send network order failed', [
                            'trade_id' => $trade['id']
                        ]);
                        throw new \ErrorException("push order failed", ['order_id' => $trade['id'], 'attempts' => $this->attempts()]);
                    }
                } else {
                    //订单已过期，用户可能已经离开了
                    Log::warning('@@order timeout', [
                        'trade_id' => $trade['id'],
                        'payment_at' => $trade['payment_at'],
                        'now' => '' . Carbon::now()
                    ]);
                    $this->delete();
                }
            }
        }
    }
}
