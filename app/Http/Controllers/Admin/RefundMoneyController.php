<?php

namespace App\Http\Controllers\Admin;

use App\Models\Trade;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;

class RefundMoneyController extends Controller
{
    public function test()
    {
        $trades = Trade::needRefundMoney()->get();
        foreach ($trades as $trade)
        {
            $this->refund($trade);
        }

        return 'hello';
    }

    public function refund($trade)
    {
        $trade->refund_money = $trade->payment_money;
        $trade->refund_remark = '超时自动退款';

//        提交退款单到支付宝
        $alipayOrder = [
            'trade_no' => $trade->payment_trade_id,
            'out_trade_no' => $trade->id,
            'out_request_no' => $trade->id,
            'refund_amount' => to_float($trade->refund_money),
            'refund_reason' => $trade->refund_remark,
            'store_id' => $trade->device_id,
        ];

        Log::debug($alipayOrder);

        $trade->refund_status = Trade::RefundStatus_Processing;
        $trade->save();

        $alipay = Pay::alipay($this->config());
        $result = $alipay->refund($alipayOrder);

        if ($result['fund_change'] === 'Y')
        {
            $trade->refund_status = Trade::RefundStatus_Success;
            $trade->refund_money = to_int($result['refund_fee']);
            $trade->refund_at = Carbon::createFromFormat('Y-m-d H:i:s', $result['gmt_refund_pay']);
            $trade->updateInfo();

//            更新退款状态
            $text = 'ai'.implode('.', [
                    $trade->id,
                    $trade->device_id,
                    $trade->user_id,
                    $trade->owner_id,
                    $trade->payment_trade_id,
                    $trade->refund_money,
                    $trade->refund_at,
                ]);

//            退款签名
            $trade->refund_signature = bcrypt($text);
            $trade->save();
        }
        Log::debug('refund', $result);
        return 'hello';
    }

    protected function config()
    {
        return [
            'app_id' =>  config('alipay.app_id'),
//            'notify_url' => route('payment.alipay.notify'),
//            'return_url' => route('payment.alipay.return'),
            'ali_public_key' => config('alipay.ali_public_key'),
            'private_key' => config('alipay.private_key'),
            'log' => [
                'file' => './logs/alipay_refund.log',
                'level' => 'debug'
            ],
            'mode' => 'dev',
        ];
    }
}
