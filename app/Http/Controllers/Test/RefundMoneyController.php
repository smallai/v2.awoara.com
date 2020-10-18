<?php

namespace App\Http\Controllers\Test;

use App\Models\Trade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

//Collection {#1337 ▼
//    #items: array:10 [▼
//"code" => "10000"
//"msg" => "Success"
//"buyer_logon_id" => "ovh***@sandbox.com"
//"buyer_user_id" => "2088102175267483"
//"fund_change" => "Y"
//"gmt_refund_pay" => "2018-05-25 16:38:19"
//"out_trade_no" => "1805251631433652"
//"refund_fee" => "1.41"
//"send_back_fee" => "0.00"
//"trade_no" => "2018052521001004480200505980"
//]
//}
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

        debugbar()->debug($alipayOrder);

        $trade->refund_status = Trade::RefundStatus_Processing;
        $trade->updateInfo();
        $trade->save();

        $alipay = Pay::alipay($this->config());
        $result = $alipay->refund($alipayOrder);

        if ($result['fund_change'] === 'Y')
        {
            $trade->refund_status = Trade::RefundStatus_Success;
            $trade->refund_money = to_int($result['refund_fee']);
            $trade->refund_code = $result['code'];
            $trade->refund_at = $result['gmt_refund_pay'];
            $trade->updateInfo();

//            更新退款状态
            $text = implode('.', [
                    'ai',
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

        debugbar()->debug($result);
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
