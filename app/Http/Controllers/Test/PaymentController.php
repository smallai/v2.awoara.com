<?php

namespace App\Http\Controllers\Test;

use App\Models\Device;
use App\Models\Goods;
use \App\Utils\IdGenerator;
use App\Models\LogUserLogin;
//use App\Order;
//use App\OrderGoods;
//use App\OrderRefund;
use App\Scopes\DeviceScope;
use App\Models\Trade;
use App\Models\User;
use App\Models\UserVipCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Facades\Agent;
use Yansongda\Pay\Pay;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    protected $alipay_config = null;

    public function __construct()
    {
        $this->alipay_config = [
	        'charset' => 'utf-8',
            'app_id' =>  config('alipay.app_id'),
            'notify_url' => route('payment.alipay.notify'),
            'return_url' => route('payment.alipay.return'),
            'ali_public_key' => config('alipay.ali_public_key'),
            'private_key' => config('alipay.private_key'),
            'log' => [
                'file' => storage_path('logs/alipay_payment.log'),
                'level' => 'debug'
            ],
//            'mode' =>  'dev',
        ];

        if (config('alipay.sandbox'))
        {
            $this->alipay_config['mode'] = 'dev';
        }
    }

    //下单界面
    public function index(Request $request)
    {
//        if (isWeChat())
//        {
//            return redirect('https://wechat.awoara.com/pay?id='.$request->input('id', -1));
//        }
//        else
//        {
//            return redirect('https://alipay.awoara.com/pay?id='.$request->input('id', -1));
//        }

        Log::debug('request', $request->all());

        if (isWeChat())
        {
            return redirect()->route('wechat.showGoods', ['device_id' => $request->input('id', -1)]);
        }
        else if (isAlipay())
        {
            return redirect()->route('alipay.showGoods', ['device_id' => $request->input('id', -1)]);
        }
        else
        {
            return redirect()->route('alipay.error')->withErrors(['请使用支付宝或微信扫码！']);
        }

        $device_id = $request->input('id', -1);
        $device = Device::findOrFail($device_id);
        Session::put('device_id', $device_id);
//        return Device::findOrFail($device_id);;

        $items = $device->goods()->where('is_sale', true)->get();
        return view('wap.pay', compact('device', 'items'));
    }

    private function paymentType()
    {
        if (isAlipay())
            return Trade::PaymentType_Alipay;
        else if (isWeChat())
            return Trade::PaymentType_WeChat;
        else
            return Trade::PaymentType_Alipay;
    }

    //下单操作
    public function order(Request $request)
    {
        list($goods_id, $goods_price) = explode(',', $request->input('goods', '-1'));

        $device_id = $request->input('device_id', -1);
        $device = Device::find($device_id);
        if (!$device)
        {
            return view('wap.error')->withErrors(['设备未注册，设备编号: '.$device_id]);
        }

        $goods = Goods::find($goods_id);
        if (!$goods)
        {
            return view('wap.error')->withErrors(['商品已下架，请重新下单！']);
        }

        //价格已经发生变动，需要回到下单界面
        if (to_int($goods_price) !== $goods->price)
        {
            return view('wap.error')->withErrors(['商品信息已过期，请重新下单！']);
        }

        $user = Auth::user();
        if (isset($user))
        {
            debugbar()->debug($user);

            $logUserLogin = LogUserLogin::where('user_id', $user->id)->latest()->first();
            debugbar()->debug($logUserLogin);
        }
        else if ($goods->count > 1) //多次洗车，必须登录
        {
            return view('wap.error')->withErrors(['暂不支持多次的套餐，请重新下单！']);
        }

        $trade = new Trade();

       try
        {
            DB::beginTransaction();

//            创建交易记录
            $trade->id = IdGenerator::tradeId();
            $trade->user_id = isset($user) ? $user->id : null;
            $trade->owner_id = $device->owner_id;
            $trade->device_id = $device->id;
            $trade->washcar_id = null;
            $trade->goods_id = $goods->id;
            $trade->log_user_login_id = isset($logUserLogin) ? $logUserLogin->id : null;

            $trade->payment_type = $this->paymentType();
            $trade->payment_status = Trade::PaymentStatus_Processing;
            $trade->confirm_status = Trade::GoodsStatus_None;
            $trade->refund_status = Trade::RefundStatus_None;
            $trade->withdraw_status = Trade::WithdrawStatus_None;

            $trade->user_ip = $request->getClientIp();
            $trade->user_phone = isset($user) ? $user->phone : null;
            $trade->user_email = isset($user) ? $user->email : null;
            $trade->user_openid = null;

            $trade->is_self = $device->is_self;
            $trade->goods_name = $goods->name;
            $trade->goods_price = $goods->price;
            $trade->goods_image = $goods->image;
            $trade->goods_is_sale = $goods->is_sale;
            $trade->goods_is_recommend = $goods->is_recommend;
            $trade->goods_seconds = $goods->seconds;
            $trade->goods_count = $goods->count;
            $trade->goods_days = $goods->days;

            $trade->payment_money = $goods->price;
            $trade->platform_fee_rate = $device->platform_fee_rate;
            $trade->updateInfo();
            $trade->saveOrFail();

            DB::commit();
        }
       catch (\Exception $exception)
       {
           Log::error($exception);
           DB::rollBack();
           return view('wap.error')->withErrors(['系统忙碌，请稍后重试！']);
       }

        //提交订单给支付宝
        $order = [
            'store_id' => $trade->device_id,
            'out_trade_no' => $trade->id,
            'total_amount' => to_float($trade->payment_money),
            'subject' => '萌芽洗车',
        ];

//       dd($this->alipay_config);

       if (Agent::isDesktop()) {
           $alipay = Pay::alipay($this->alipay_config)->web($order);
       }
       else {
           $alipay = Pay::alipay($this->alipay_config)->wap($order);
       }

       Log::debug($alipay);

        return $alipay;
    }

//    支付宝支付完成的同步跳转,同步跳转不需要检查交易状态，肯定是付款成功了的。
    public function alipayReturn(Request $request)
    {
        $paymentSuccess = false;
        $sendOrderSuccess = false;

       try
        {
            Log::debug('alipay return begin');

            $alipay = Pay::alipay($this->alipay_config);
            $data = $alipay->verify();
            $trade = Trade::findOrFail($data->out_trade_no);

            //检查是否支付成功
            if ($this->checkTradeIsValid($data, $trade))
            {
                $paymentSuccess = $this->saveTradeInfo($data, $trade);
                $sendOrderSuccess = $this->sendOrderInfo($trade);
            }
        }
       catch (\Exception $e)
       {
           debug_backtrace();
       }

       Log::debug('alipay return success');
       return view('wap.success', compact('paymentSuccess', 'sendOrderSuccess', 'trade'));
    }

    //https://docs.open.alipay.com/289/105656
    public function alipayRedirect(Request $request)
    {
        $input = $request->all();
        return response()->json($input);
    }

    // 支付宝要求支付
    //https://tech.open.alipay.com/support/knowledge/index.htm?categoryId=24121&knowledgeId=201602074339&scrollcheck=1#/?_k=oigaj1
    public function alipayNotifyUrl(Request $request)
    {
        try
        {
            \Log::debug('alipay notify begin', [
                'request' => $request->request->all(),
                'query' => $request->query->all()
            ]);

            $alipay = Pay::alipay($this->alipay_config);
            $data = $alipay->verify();
            $trade = Trade::find($data->out_trade_no);

        	\Log::warning('alipay_notify', [
            		$data
        	]);

            if (($data->trade_status === 'TRADE_SUCCESS') || ($data->trade_status === 'TRADE_FINISHED'))
            {
                //检查是否支付成功
                if ($this->checkTradeIsValid($data, $trade))
                {
                    $paymentSuccess = $this->saveTradeInfo($data, $trade);
                    $sendOrderSuccess = $this->sendOrderInfo($trade);

                    Log::debug('alipay notify success',  [$trade]);
                    return $alipay->success();
                }
            }
        }
        catch (\Exception $e)
        {
        	\Log::warning('exception', [$e]);
        }

        \Log::warning('notify failed', [
            $request
        ]);

        Log::debug('alipay notify failed');
        return 'fail';
    }

    /*
{
"code": "10000",
"msg": "Success",
"buyer_logon_id": "hon***@163.com",
"buyer_pay_amount": "0.00",
"buyer_user_id": "2088602337155527",
"invoice_amount": "0.00",
"out_trade_no": "1809021048028522",
"point_amount": "0.00",
"receipt_amount": "0.00",
"send_pay_date": "2018-09-02 10:48:07",
"total_amount": "0.01",
"trade_no": "2018090222001455521000841518",
"trade_status": "TRADE_SUCCESS"
}
*/
    public function findOrder(Request $request)
    {
        $tradeId = 1809021048028522;
        $alipay = Pay::alipay($this->alipay_config);
        return $alipay->find($tradeId);
    }

//    支付宝退款的通知接口
    public function refundNotifyUrl(Request $request)
    {
        return 'failed';
    }

//    public function refund()
//    {
//        return $this->orderRefund(1805241647274411, 100, '设备故障') ? '退款成功' : '退款失败';
//    }

//    退款操作
//1805241647274411 1
//    public function orderRefund($order_id, $refund_money, $refund_reason)
//    {
//       try
//        {
//            \DB::beginTransaction();
//
//            $order = Order::findOrFail($order_id);
//            $oldTrade = Trade::where('ext_id', $order->id)
//                ->where('trade_type', Trade::TradeType_Order)
//                ->where('payment_status', Trade::PaymentStatus_Success)->first();
//
////            更新退款状态
//            $order->refund_status = Trade::RefundStatus_Processing;
//            $order->save();
//
////            创建退款记录
//            $refund = new OrderRefund();
//            $refund->id = IdGenerator::refundId();
//            $refund->user_id = $order->user_id;
//            $refund->owner_id = $order->owner_id;
//            $refund->device_id = $order->device_id;
//            $refund->order_id = $order->id;
//            $refund->refund_status = Trade::RefundStatus_Processing;
//            $refund->refund_money = $refund_money;
//            $refund->refund_remark = $refund_reason;
//            $refund->refund_at = Carbon::now();
//            $refund->save();
//
//            debugbar()->debug($oldTrade);
//
////            创建交易记录
//            $newTrade = new Trade();
//            $newTrade->id = IdGenerator::tradeId();
//            $newTrade->user_id = $oldTrade->user_id;
//            $newTrade->owner_id = $oldTrade->owner_id;
//            $newTrade->device_id = $oldTrade->device_id;
//            $newTrade->washcar_id = null;
//            $newTrade->ext_id = $refund->id;
//            $newTrade->trade_type = Trade::TradeType_Refund;
//            $newTrade->payment_type = $oldTrade->payment_type;
//            $newTrade->payment_status = Trade::PaymentStatus_Processing;
//            $newTrade->save();
//
//            debugbar()->debug($newTrade);
//
//            \DB::commit();
//
////            提交退款单到支付宝
//            $alipayOrder = [
//                'trade_no' => $oldTrade->payment_trade_id,
//                'out_trade_no' => $oldTrade->id,
//                'out_request_no' => $refund->id,
//                'refund_amount' => to_float($refund->refund_money),
//                'refund_reason' => $refund_reason,
//                'store_id' => $order->device_id,
//            ];
//
//            debugbar()->debug($alipayOrder);
//
//            $alipay = Pay::alipay($this->alipay_config);
//            $result = $alipay->refund($alipayOrder);
//
//            \DB::beginTransaction();
//
////            退款金额发生变化
//            if ($result['fund_change'] === 'Y')
//            {
////                更新退款单退款状态
//                $refund->refund_status = Trade::RefundStatus_Success;
//                $refund->save();
////                更新交易状态
//                $newTrade->payment_status = Trade::PaymentStatus_Success;
//                $newTrade->payment_trade_id = $result['trade_no'];
//                $newTrade->payment_money = $refund->refund_money;
//                $newTrade->payment_at = $result['gmt_refund_pay'];
//                $newTrade->save();
//            }
//
////            更新订单退款状态
//            $order->refund_status = Trade::RefundStatus_Success;
//            $order->refund_money = to_int($result['refund_fee']);
//            $order->updateInfo();
//            $order->save();
//
//            debugbar()->debug($order);
//
//            \DB::commit();
//
//            debugbar()->debug($result);
//            return true;
////            return $result;
//        }
//       catch (\Exception $exception)
//       {
//           \DB::rollBack();
//           Log::error('refund error', [$exception]);
//       }
//
//        return false;
//    }

//Collection {#426 ▼
//    #items: array:5 [▼
//"code" => "10000"
//"msg" => "Success"
//"order_id" => "20180502110070001502160000114264"
//"out_biz_no" => "1525248050"
//"pay_date" => "2018-05-02 16:00:51"
//]
//}
    //out_biz_no 系统的提现记录号
    //payee_account 收款账号
    //amount 收款金额
    //payer_show_name 显示的付款方名称
    //payee_real_name 收款人的真实姓名
    //remark 备注信息
    public function payee()
    {
        $order = [
            'out_biz_no' => time(),
            'payee_type' => 'ALIPAY_LOGONID',
            'payee_account' => 'ovhtuy5698@sandbox.com',
            'amount' => '0.01',
            'payer_show_name' => '萌芽洗车',
            'payee_real_name' => '沙箱环境',
            'remark' => '洗车机收益',
        ];

        $alipay = Pay::alipay($this->alipay_config);
        $result = $alipay->transfer($order);
        echo "<pre>";
        dd($result);
        echo "</pre><br>";
    }

    // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
    // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
    // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
    // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
    // 4、验证app_id是否为该商户本身。
    // 5、其它业务逻辑情况
    private function checkTradeIsValid($data, $trade)
    {
        if ($data->out_trade_no != $trade->id)
        {
            \Log::warning('out_trade_no error', [$data, $trade]);
            return false;
        }

        if ($data->seller_id != config('alipay.seller_id'))
        {
            \Log::warning('seller_id error', [$data, $trade]);
            return false;
        }

        if ($data->app_id != config('alipay.app_id'))
        {
            \Log::warning('app_id error', [$data, $trade]);
            return false;
        }

        if (0 != bccomp($data->total_amount, to_float($trade->payment_money)))
        {
            \Log::warning('total_amount error', [$data, $trade]);
            return false;
        }

        return true;
    }

    //保存支付信息
    private function saveTradeInfo($data, Trade $trade)
    {
        //正在付款状态
        if ($trade->payment_status !== Trade::PaymentStatus_Success)
        {
            //设置为付款成功，保存付款信息

           try
            {
                DB::beginTransaction();

                $trade->payment_status = Trade::PaymentStatus_Success;
                $trade->payment_trade_id = $data->trade_no;
                $trade->payment_at = $data->timestamp;
                $trade->updateInfo();

//                收款签名
                $trade->paySign();
                $trade->save();

                //保存按次洗车的相关信息
//                $this->saveVipCardInfo($trade);

                DB::commit();
            }
           catch (\Exception $exception)
           {
               DB::rollBack();
               Log::error('save payment info', [$exception]);
           }

            debugbar()->debug('PayStatus_Success');
			return true;
        }

        return false;
    }

    public function saveVipCardInfo($trade)
    {
        if ($trade->count > 1)
        {
            $vipCard = new UserVipCard();
            $vipCard->id = IdGenerator::vipCardId();
            $vipCard->user_id = $trade->user_id;
            $vipCard->owner_id = $trade->owner_id;
            $vipCard->device_id = $trade->device_id;
            $vipCard->trade_id = $trade->id;
            $vipCard->log_user_login_id = $trade->log_user_login_id;
            $vipCard->seconds = $trade->goods_seconds;
            $vipCard->days = $trade->goods_days;
            $vipCard->used_count = 0;
            $vipCard->total_count = $trade->goods_count;
            $vipCard->expiration = Carbon::now()->addDays($trade->goods_days);
            $vipCard->save();
        }
    }

    public function wechatNotifyUrl(Request $request)
    {
        die(403);
    }

    //发送订单给设备
    private function sendOrderInfo($trade)
    {
        //未确认收货状态
        if ($trade->confirm_status !== Trade::GoodsStatus_Confirmed)
        {
			$t1 = microtime(true);
            $iotDevice = new \App\Utils\IotDevice($trade->device_id);
            if ($iotDevice->netOrder($trade->id, $trade->goods_seconds))
            {
                try {
                    DB::beginTransaction();

                    $trade->confirm_status = Trade::GoodsStatus_Confirmed;
                    $trade->confirmed_at = Carbon::now();
                    $trade->save();

                    //多次洗车则增加使用次数
//                    if ($trade->goods_count > 1)
//                    {
//                        $vipCard = UserVipCard::where('trade_id', $trade->id)->latest()->first();
//                        if (isset($vipCard))
//                        {
//                            $vipCard->used_count++;
//                            $vipCard->save();
//                        }
//                    }

                    DB::commit();
                }
                catch (\Exception $exception)
                {
                    DB::rollBack();
                }
                debugbar()->debug('GoodsStatus_Confirmed');
            }

			$t2 = microtime(true);
			debugbar()->debug(''.'耗时'.bcsub($t2, $t1, 3).'秒');
        }

		return Trade::GoodsStatus_Confirmed === $trade->confirm_status;
    }

    private function deviceStatus($device_id)
    {
        $device = Device::findOrFail($device_id);
        return $device['status'];
    }

//    private function gateway()
//    {
//        $gateway = \Omnipay\Omnipay::create('Alipay_AopWap');
//        if (env('APP_DEBUG')) {
//            $gateway->sandbox();
//        }
//
//        $gateway->setSignType('RSA2'); // RSA/RSA2/MD5
//        $gateway->setAppId(env('ALIPAY_APP_ID'));
//        $gateway->setPrivateKey(env('ALIPAY_PRIVATE_KEY'));
//        $gateway->setAlipayPublicKey(env('ALIPAY_PUBLIC_KEY'));
//        $gateway->setReturnUrl(route('payment.alipay.return'));
////        $gateway->setNotifyUrl(route('payment.notify_url'));
//
//        return $gateway;
//    }

    //已付款的记录
    public function scopePayment($query)
    {
        return $query->whereIn('payment_status', [
            Trade::PaymentStatus_Success
        ]);
    }

    //已退款的记录
    public function scopeRefund($query)
    {
        return $query->whereIn('refund_status', [
            Trade::RefundStatus_Success
        ]);
    }

    //已提现的记录
    public function scopeCash($query)
    {
        return $query->whereIn('refund_status', [
            Trade::WithdrawStatus_Success
        ]);
    }

    //正在提现的记录
    public function scopeCashing($query)
    {
        return $query->whereIn('refund_status', [
            Trade::WithdrawStatus_Request,
            Trade::WithdrawStatus_Confirmed,
            Trade::WithdrawStatus_Processing,
        ]);
    }
}
