<?php

namespace App\Http\Controllers\Alipay;

use App\Models\Device;
use App\Models\Goods;
use \App\Utils\IdGenerator;
use App\Utils\IotDevice;
use App\Jobs\QueryAlipayOrderJob;
use App\Jobs\RefreshIotDeviceStateJob;
use App\Models\Trade;
use App\Models\UserVipCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Facades\Agent;
use Yansongda\Pay\Pay;

class AlipayController extends Controller
{
    /*
     * 显示商品列表
     * */
    public function showGoods(Request $request)
    {
        $device_id = $request->input('device_id', -1);
        if ($device_id < 0) {
            $device_id = $request->input('id', -1);
        }

        //查找设备
        $device = Device::find($device_id);
        if (!$device)
        {
            return view('alipay.error')->withErrors([ '设备未注册，请稍后重试！设备编号: '.$device_id ]);
        }

        Session::put('device.id', $device_id);

        //刷新状态
        dispatch(new RefreshIotDeviceStateJob($device->id));

        //是否已停用
        if (Device::DeviceStatus_Disable === $device->status)
        {
            return view('alipay.error')->withErrors(['设备维护，请稍后重试！']);
        }

        //检查是否在线
        if (!$this->isOnline($device))
        {
            return view('alipay.error')->withErrors(['设备离线,请稍后重试！']);
        }

        //２０１８－０９－１４　由于回调的时候，是支付宝回调的，所以需要提前保存用户的IP
        //保存扫码的时候的用户ip
        Session::put('user_ip', $request->getClientIp());

        $goods = [];
        $today_count = 0;
        //如果有会员卡，跳转到会员卡支付界面去
        $card = GetUserVipCard($this->openid());
        Log::debug('card', ['card' => $card]);
        if (VipCardIsActive($device, $card))
        {
            //计算今天使用了多少次了
//            \DB::enableQueryLog();
            $today_count = Trade::whereDate('created_at', (string)Carbon::today())
                ->where('user_openid', '=', $this->openId())
                ->where('card_id', '=', $card->id)
                ->where('payment_type', Trade::PaymentType_VipCard)
                ->count();
            $logs = \DB::getQueryLog();
//            \DB::disableQueryLog();
//            Log::debug($logs);

            Log::debug('data', [
                'today_count' => $today_count,
                'today_limit' => $card->today_limit,
            ]);
            if ($today_count < $card->today_limit)
            {
                return redirect()->route('alipay.showCardInfo', [
                    'device_id' => $device['id'],
                    'card_id' => $card['id']
                ]);
            }
            else
            {
                $goods = $goods = $device->goods()->where('is_sale', true)->where('count', 1)->orderBy('price')->get();
            }
        }

        //超出了今天的使用次数,那么只能购买单次洗车的啦！
        if (count($goods) <= 0)
        {
            $goods = $device->goods()->where('is_sale', true)->orderBy('price')->get();
        }

        //查找商品
        if (count($goods) <= 0)
        {
            return view('alipay.error')->withErrors(['设备价格未设置,请稍后重试！']);
        }

        if (count($goods) === 1)
        {
            $goods[0]->is_recommend = true;
        }

        //生成视图
        $items = $goods;
        Log::debug('goods', [$items]);
        return view('alipay.showGoods', compact('card', 'device', 'items', 'today_count'));
    }

    /*
     * 下单，跳到付款界面
     * */
    public function order(Request $request)
    {
        $device_id = $request->input('device_id', -1);

        //查找设备
        $device = Device::find($device_id);
        if (!$device)
        {
            return view('alipay.error')->withErrors([ '设备未注册，请稍后重试！设备编号: '.$device_id ]);
        }

        //是否已停用
        if (Device::DeviceStatus_Disable === $device->status)
        {
            return view('alipay.error')->withErrors(['设备维护，请稍后重试！']);
        }

        //检查是否在线
        if (!$this->isOnline($device))
        {
            return view('alipay.error')->withErrors(['设备离线,请稍后重试！']);
        }

        list($goods_id, $goods_price) = explode(',', $request->input('goods', '-1,0'));

        //查找商品
        $goods = Goods::find($goods_id);
        if (!$goods)
        {
            return view('wechat.error')->withErrors(['商品已下架，请重新下单！']);
        }

        //检查商品价格
        $goods_price = (int)($goods_price * 100);
        if ($goods->price !== $goods_price)
        {
            return view('wechat.error')->withErrors(['价格信息已过期，请重新下单！']);
        }

        //生成订单
        $trade = $this->createTrade();
        $this->saveDeviceInfo($trade, $device);
        $this->saveGoodsInfo($trade, $goods);
        $this->saveUserInfo($trade, Auth::user());
        $trade->updateInfo();
        try {
            $trade->saveOrFail();
        }
        catch (\Exception $exception)
        {
            Log::error($exception);
            return view('wechat.error')->withErrors(['服务器忙碌，请稍后重试！']);
        }

        //提交订单给支付宝
        $order = [
            'store_id' => $trade->device_id,
            'out_trade_no' => $trade->id,
            'total_amount' => to_float($trade->payment_money),
            'subject' => '萌芽洗车',
        ];

        $config = config('alipay');
        if (Agent::isDesktop()) {
            $alipay = Pay::alipay($config)->web($order);
        }
        else {
            $alipay = Pay::alipay($config)->wap($order);
        }

        dispatch(new QueryAlipayOrderJob($trade['id']));
        dispatch(new QueryAlipayOrderJob($trade['id']));
        Log::debug($alipay);

        return $alipay;
    }

    /*
     * 用户付款后的同步跳转
     * */
    public function returnUrl(Request $request)
    {
        Log::debug('returnUrl', $request->all());

        $trade = null;

//        try
        {
            Log::debug('alipay return begin');

            $config = config('alipay');
            $alipay = Pay::alipay($config);
            $data = $alipay->verify();
            $trade = Trade::findOrFail($data['out_trade_no']);

            //检查是否支付成功
            if ($this->checkTradeIsValid($data, $trade)) {
                $this->updatePaymentStatus($trade, $data);

//                if ($trade['goods_count'] > 1)
                {
                    $card = $this->saveUserVipCardInfo($trade);
                    $this->Process($trade, $card);
                    return redirect()->route('alipay.showCardInfo', [
                        'device_id' => $trade['device_id'],
                        'card_id' => $card['id'],
                    ]);
                }
//                else
//                {
//                    $this->Process($trade);
//                    return view('alipay.success', compact('trade'));
//                }
            }
        }
//        catch (\Exception $exception)
//        {
//            Log::error('returnUrl exception', [ $exception]);
//        }

        Log::debug('alipay return success');

        return view('alipay.error')->withErrors('系统忙碌，稍后重试！');
    }

    /*
     * 用户付款后的异步通知
     * */
    public function notifyUrl(Request $request)
    {
        try
        {
            \Log::debug('alipay notifyUrl', $request->all());

            $config = config('alipay');
            $alipay = Pay::alipay($config);
            $data = $alipay->verify();
            $trade = Trade::find($data['out_trade_no']);

            if (($data['trade_status'] === 'TRADE_SUCCESS')
                || ($data['trade_status'] === 'TRADE_FINISHED')) {
                //检查是否支付成功
                if ($this->checkTradeIsValid($data, $trade)) {
                    $this->updatePaymentStatus($trade, $data);
//                    $this->dispatch(new PushMessageTask($trade->id));
                    $card = $this->saveUserVipCardInfo($trade);
                    $this->Process($trade, $card);
//                    $this->processMore($trade);
                    Log::debug('alipay notify success', [$trade]);
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
     * 支付宝退款流程
     * */
    public static function refundMoney(Trade $trade, $money, $reason)
    {
        //检查是不是支付宝支付的记录
        if ($trade['payment_type'] !== Trade::PaymentType_Alipay) {
            Log::error('payment type error');
            flash()->warning('付款类型错误');
            return;
        }

        //检查是不是支付成功了的
        if ($trade['payment_status'] !== Trade::PaymentStatus_Success) {
            Log::error('payment status error');
            flash()->warning('付款状态错误');
            return;
        }

        //检查是不是未退款状态
        if ($trade['refund_status'] !== Trade::RefundStatus_None) {
            Log::error('refund status error');
            flash()->warning('退款状态正在退款');
            return;
        }

        //检查金额是否正确
        if ($money <= 0) {
            Log::error('refund money error1');
            flash()->warning('退款金额错误1');
            return;
        }

        //检查金额是否正确
        if ($money > $trade['payment_money']) {
            Log::error('refund money error2');
            flash()->warning('退款金额错误2');
            return;
        }

        //去支付宝查一下金额是否正确！！！
        if (!self::checkAlipayOrder($trade)) {
            Log::error('check alipay order error');
            return;
        }

        //先保存为正在处理中
        $trade['refund_operator_id'] = Auth::user()->id;
        $trade['refund_id'] = IdGenerator::refundId();
        $trade['refund_status'] = Trade::RefundStatus_Success;
        $trade['refund_money'] = $money;
        $trade->updateInfo();
        $trade->save();

//        Log::debug('money', ['money' => $money, 'refund_money' => $trade['refund_money']]);

        //创建退款单
        $alipayOrder = [
            'trade_no' => $trade['payment_trade_id'],
            'out_trade_no' => $trade['id'],
            'out_request_no' => $trade['refund_id'],
            'refund_amount' => to_float($trade['refund_money']),
            'refund_reason' => $reason,
            'store_id' => $trade['device_id'],
        ];

        //向支付宝服务器提交退款单
        $config = config('alipay');
        $alipay = Pay::alipay($config);

        Log::debug('alipay refund', ['order' => $alipayOrder, 'config' => $config]);

        $result = $alipay->refund($alipayOrder);

        Log::info('alipay refund', [
            'trade' => $trade,
            'result' => $result,
        ]);

        //款金额发生变化
        if ($result['fund_change'] === 'Y') {
            $trade['refund_status'] = Trade::RefundStatus_Success;
            $trade['refund_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $result['gmt_refund_pay']);
        }

        //再更新为处理完成
        $trade['refund_money'] = to_int($result['refund_fee']);
        $trade->updateInfo();
        $trade->save();

        flash()->success('退款申请已提交');
    }

    /*
     * 更新设备在线状态
     * */
    protected function isOnline(Device $device)
    {
        $iot = new IotDevice($device['id']);
        $device['is_online'] = $iot->isOnline();
        $device->save();
        return $device['is_online'];
    }

    /*
     * 创建订单
     * @return App\Models\Trade
     * */
    protected function createTrade() : Trade
    {
        $trade = new Trade();

        $trade['id'] = IdGenerator::tradeId();
        $trade['washcar_id'] = null;
        $trade['payment_type'] = Trade::PaymentType_Alipay;
        $trade['payment_status'] = Trade::PaymentStatus_Processing;
        $trade['confirm_status'] = Trade::GoodsStatus_None;
        $trade['refund_status'] = Trade::RefundStatus_None;
        $trade['withdraw_status'] = Trade::WithdrawStatus_None;

        return $trade;
    }

    protected function saveDeviceInfo(Trade $trade, Device $device)
    {
        $trade['owner_id'] = $device['owner_id'];
        $trade['device_id'] = $device['id'];
        $trade['is_self'] = $device['is_self'];
        $trade['platform_fee_rate'] = $device['platform_fee_rate'];
    }

    protected function saveGoodsInfo(Trade $trade, Goods $goods)
    {
        $trade['goods_id'] = $goods['id'];
        $trade['goods_name'] = $goods['name'];
        $trade['goods_price'] = $goods['price'];
        $trade['goods_image'] = $goods['image'];
        $trade['goods_is_sale'] = $goods['is_sale'];
        $trade['goods_is_recommend'] = $goods['is_recommend'];
        $trade['goods_seconds'] = $goods['seconds'];
        $trade['goods_count'] = $goods['count'];
        $trade['goods_days'] = $goods['days'];
        $trade['payment_money'] = $goods['price'];
    }

    protected function saveUserInfo(Trade $trade, $user)
    {
        if ($user)
        {
            $trade['user_phone'] = $user['phone'];
            $trade['user_email'] = $user['email'];
        }
        $trade['user_ip'] = \Illuminate\Support\Facades\Request::getClientIp();
        $trade['user_openid'] = $this->openId();
    }

    /*
     * 支付宝用户的openid
     * */
    protected function openid() : string
    {
        $user = Session::get('alipay.oauth_user.default'); // 拿到授权用户资料
        Log::debug('alipay user', ['user' => $user] );
        return $user ? $user['id'] : str_random(64);
    }

    /*
     * 更新支付状态
     * */
    protected function updatePaymentStatus(Trade $trade, $data) : bool
    {
        if ($trade['payment_status'] !== Trade::PaymentStatus_Success) {
            $trade['payment_status'] = Trade::PaymentStatus_Success;
            $trade['payment_at'] = Carbon::now();
            $trade['payment_trade_id'] = $data['trade_no'];
            $trade->updateInfo();
            $trade->paySign();
            $trade->save();
        }

//        $this->dispatch(new PushMessageTask($trade->id));

        return $trade['payment_status'] === Trade::PaymentStatus_Success;
    }

    //处理单次洗车的记录
    protected function Process(Trade $trade, UserVipCard $card)
    {
        if (($trade['payment_status'] == Trade::PaymentStatus_Success)) {
            //没有推送消息
            if ($trade['confirm_status'] != Trade::GoodsStatus_Confirmed) {
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

                        $card->used_count++;
                        $card->save();

                        Log::info('send network order success', [
                            'trade_id' => $trade['id']
                        ]);
                    } else {
                        //推送消息失败
                        Log::warning('send network order failed', [
                            'trade_id' => $trade['id']
                        ]);
                    }
                } else {
                    //订单已过期，用户可能已经离开了
                    Log::warning('@@order timeout', [
                        'trade_id' => $trade['id'],
                        'payment_at' => $trade['payment_at'],
                        'now' => '' . Carbon::now()
                    ]);
                }
            }
        }
    }

//    //处理可以多次使用的记录
//    protected function processMore(Trade $trade)
//    {
//        if (($trade['payment_status'] === Trade::PaymentStatus_Success)
//            && ($trade['goods_count'] > 1))
//        {
//            $trade['confirm_status'] = Trade::GoodsStatus_Confirmed;
//            $trade['confirmed_at'] = Carbon::now();
//            $trade->save();
//        }
//    }

    // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为
    // TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
    // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
    // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
    // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方
    //（有的时候，一个商户可能有多个seller_id/seller_email）；
    // 4、验证app_id是否为该商户本身。
    // 5、其它业务逻辑情况
    protected function checkTradeIsValid($data, $trade)
    {
        $config = config('alipay');

        if ((int)($data['out_trade_no']) !== $trade['id'])
        {
            \Log::warning('out_trade_no error', [$data, $trade]);
            return false;
        }

        if ($data['seller_id'] !== $config['seller_id'])
        {
            \Log::warning('seller_id error', [$data, $trade]);
            return false;
        }

        if ($data['app_id'] !== $config['app_id'])
        {
            \Log::warning('app_id error', [$data, $trade]);
            return false;
        }

        if (0 !== bccomp($data['total_amount'], to_float($trade['payment_money'])))
        {
            \Log::warning('total_amount error', [$data, $trade]);
            return false;
        }

        //向支付宝查询订单信息
        $alipay = Pay::alipay($config);
        $result = $alipay->find($trade->id);

        if ((int)($result['out_trade_no']) !== $trade['id'])
        {
            Log::warning('out_trade_no error', [$result, $trade]);
            return false;
        }

        if (0 !== bccomp($result['total_amount'], to_float($trade['payment_money'])))
        {
            Log::warning('total_amount error', [$result, $trade]);
            return false;
        }

        //交易状态为已付款
        if (($result['trade_status'] === 'TRADE_SUCCESS')
            || ($result['trade_status'] === 'TRADE_FINISHED'))
        {
            Log::info('trade paid: '.$trade->id);
            return true;
        }

        Log::info('trade no_paid: '.$trade->id);
        return false;
    }

    /*
     * 向支付宝查询一下用户是否真的已经付款了。
     * */
    protected static function checkAlipayOrder(Trade $trade)
    {
        //向支付宝查询订单信息
        $config = config('alipay');
        $alipay = Pay::alipay($config);
        $result = $alipay->find($trade->id);
        if ((int)($result['out_trade_no']) !== $trade['id'])
        {
            Log::warning('out_trade_no error', [$result, $trade]);
            return false;
        }
        if (0 !== bccomp($result['total_amount'], to_float($trade['payment_money'])))
        {
            Log::warning('total_amount error', [$result, $trade]);
            return false;
        }

        //交易状态为已付款
        if (($result['trade_status'] === 'TRADE_SUCCESS')
            || ($result['trade_status'] === 'TRADE_FINISHED'))
        {
            Log::info('trade paid: '.$trade->id);
            return true;
        }

        Log::info('trade no_paid: '.$trade->id);
        return false;
    }

    protected function saveUserVipCardInfo(Trade $trade)
    {
        $card = null;

        if (($trade['payment_status'] === Trade::PaymentStatus_Success))
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
