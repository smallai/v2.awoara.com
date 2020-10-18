<?php

namespace App\Jobs;

use App\Models\Goods;
use \App\Utils\IdGenerator;
use App\Models\Trade;
use App\Models\UserVipCard;
use Carbon\Carbon;
use function GuzzleHttp\Psr7\str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;

class QueryAlipayOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 30;

    public $interval = 6;

    public $order_id = 0;

    /**
     * Create a new job instance.
     * @param order_id the order id
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
            Log::debug("begin query order", ['order_id' => $this->order_id, 'attempts' => $this->attempts()]);
            sleep(5);
            $this->checkOrderState($this->order_id);
        } catch (\Exception $e) {
            if ($this->attempts() < $this->tries) {
                $this->release($this->interval);
            } else {
                $this->delete();
            }
        }
    }

    public function checkOrderState($order_id)
    {
        $order = Trade::findOrFail($order_id);
        if (Trade::PaymentType_Alipay === $order['payment_type']) {
            $this->checkAlipayOrder($order);
        }
        else {
            Log::debug('payment type error');
            $this->delete();
        }
    }

    public function checkAlipayOrder($trade) : bool
    {
        $config = config('alipay');
        $alipay = Pay::alipay($config);

        //向支付宝查询订单
        $reply = $alipay->find($trade->id);
        if ((int)($reply['out_trade_no']) !== $trade['id'])
        {
            Log::warning('out_trade_no error', [$trade['id'], $reply, $trade]);
            return false;
        }
        if (0 !== bccomp($reply['total_amount'], to_float($trade['payment_money'])))
        {
            Log::warning('total_amount error', [$reply, $trade]);
            return false;
        }

        //交易状态为已付款
        if (($reply['trade_status'] === 'TRADE_SUCCESS')
            || ($reply['trade_status'] === 'TRADE_FINISHED'))
        {
            Log::info('trade paid: '.$trade->id);
            if (Trade::PaymentStatus_Success !== $trade['payment_status'])
            {
                $trade['payment_status'] = Trade::PaymentStatus_Success;
                $trade['payment_at'] = Carbon::now();
                $trade['payment_trade_id'] = $reply['trade_no'];
                $trade->updateInfo();
                $trade->paySign();
                $trade->save();

                //开始推送开机命令的任务
                if (1 === $trade['good_count']) {
                    PushOrderJob::dispatch($trade['id']);
                }
                else {
                    $this->saveUserVipCardInfo($trade);
                }
            }

            return true;
        }

        return false;
    }

    protected function saveUserVipCardInfo(Trade $trade)
    {
        $card = null;

        if (($trade['payment_status'] === Trade::PaymentStatus_Success) && ($trade['goods_count'] > 1))
        {
            $card = UserVipCard::where('trade_id', $trade['id'])->first();
            if (!isset($card))
            {
                $goods = Goods::where('id', $trade['goods_id'])->withTrashed()->first();

                $card = new UserVipCard();
                $card['id'] = IdGenerator::vipCardId();
                $card['user_id'] = $trade['user_id'];
                $card['user_openid'] = $trade['user_openid'];
                $card['owner_id'] = $trade['owner_id'];
                $card['device_id'] = $trade['device_id'];
                $card['trade_id'] = $trade['id'];
                $card['log_user_login_id'] = $trade['log_user_login_id'];
                $card['seconds'] = $trade['goods_seconds'];
                $card['days'] = $trade['goods_days'];
                $card['used_count'] = 0;
                $card['total_count'] = $trade['goods_count'];
                $card['today_limit'] = isset($goods) ? $goods->today_limit : 3;
                $card['expiration'] = Carbon::now()->addDays($trade['goods_days']);
                $card['goods_name'] = $trade['goods_name'];
                $card->save();
            }
            else
            {
                Log::warning('user vip card already exists.', ['trade_id' => $trade['id']]);
            }
        }

        return $card;
    }
}
