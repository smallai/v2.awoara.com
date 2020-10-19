<?php

namespace App\Http\Controllers\WeChat;

use App\Http\Controllers\Alipay\VipCardController;
use App\Models\Goods;
use \App\Utils\IdGenerator;
use App\Jobs\RefreshIotDeviceStateJob;
use App\Jobs\RefundMoneyJob;
use App\Models\Trade;
use App\Models\User;
use App\Models\UserVipCard;
use Carbon\Carbon;
use EasyWeChat\Factory;
use function EasyWeChat\Kernel\Support\get_client_ip;
use EasyWeChat\Kernel\Support\XML;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Utils\IotDevice;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class WeChatPayController extends Controller
{
    /*
     * 显示商品列表
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     * */
    public function showGoods(Request $request)
    {
        //查找设备
        $device_id = $request->input('device_id', -1);
        if ($device_id < 0) {
            $device_id = $request->input('id', -1);
        }
        $device = Device::find($device_id);
        if (!$device)
        {
            return view('wechat.error')->withErrors(['设备未注册，请稍后重试！设备编号: '.$device_id]);
        }

        //是否已停用
        if (Device::DeviceStatus_Disable === $device->status)
        {
            return view('wechat.error')->withErrors(['设备维护，请稍后重试！']);
        }

        //检查是否在线
        if (!$this->isOnline($device))
        {
            return view('wechat.error')->withErrors(['设备离线,请稍后重试！']);
        }

        $today_count = 0;
        $goods = [];

        //检查用户是否有可用的会员卡，如果有，跳转到会员卡洗车流程
        $card = GetUserVipCard($this->openId());
        if (VipCardIsActive($device, $card))
        {
            //计算今天使用了多少次了
            $today_count = Trade::whereDate('created_at', (string)Carbon::today())
                ->where('user_openid', '=', $this->openId())
                ->where('card_id', '=', $card->id)
                ->where('payment_type', Trade::PaymentType_VipCard)
                ->count();

            //当天可用次数没用使用完
            if ($today_count < $card->today_limit)
            {
                return redirect()->route('wechat.showCardInfo', [
                    'device_id' => $device_id,
                    'card_id' => $card['id']
                ]);
            }
            else
            {
                $goods = $goods = $device->goods()->where('is_sale', true)->where('count', 1)->orderBy('price')->get();
            }
        }

        //超出了今天的使用次数
        if (count($goods) <= 0)
        {
            $goods = $device->goods()->where('is_sale', true)->orderBy('price')->get();
        }

        if (count($goods) <= 0)
        {
            return view('wechat.error')->withErrors(['设备价格未设置,请稍后重试！']);
        }

        if (count($goods) === 1)
        {
            $goods[0]->is_recommend = true;
        }

        //刷新一下token
        $this->getAccessToken();

        //生成视图
        $items = $goods;
        return view('wechat.showGoods', compact('device', 'items', 'card', 'today_count'));
    }

    /*
     * 下单
     * @return
     * */
    public function order(Request $request)
    {
        //查找设备
        $device_id = $request->input('device_id');
        if ($device_id < 0) {
            $device_id = $request->input('id', -1);
        }
        $device = Device::find($device_id);
        if (!$device)
        {
            return view('wechat.error')->withErrors(['设备未注册/已暂停服务，请重试！']);
        }

        //是否已停用
        if (Device::DeviceStatus_Disable === $device->status)
        {
            return view('wechat.error')->withErrors(['设备维护，请稍后重试！']);
        }

        //查找商品
        list($goods_id, $goods_price) = explode(',', $request->input('goods', '-1,0'));
        $goods = Goods::find($goods_id);
        if (!$goods)
        {
            return view('wechat.error')->withErrors(['商品已下架/未选中商品，请重新下单！']);
        }

        //检查价格是否已修改
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

        $trade->save();

        //刷新一下token
        $this->getAccessToken();

        //向微信提交订单
        $prepay_id = $this->prepay($trade);
        if (!$prepay_id)
        {
            return view('wechat.error')->withErrors(['服务器忙碌，请稍后重试！']);
        }

        $config = config('wechat.official_account.default');
        $app = Factory::officialAccount($config);

        try {
            $app->access_token->getToken();
        }
        catch (\Exception $exception)
        {
            Log::error($exception);
            return view('wechat.error')->withErrors(['服务器忙碌，请稍后重试！']);
        }
        $wx_config = $app->jssdk->buildConfig(['chooseWXPay'],
            false, false);

        $config = config('wechat.payment.default');
        $app = Factory::payment($config);

        $pay_config = $app->jssdk->sdkConfig($prepay_id);
        $bridge_config = $app->jssdk->bridgeConfig($prepay_id);

        //跳转到支付链接
        return view('wechat.payment', compact('wx_config', 'pay_config', 'bridge_config', 'trade'));
    }

    /*
     * 微信通知
     * @return string
     * */
    public function notify() : string
    {
        $wx_config = config('wechat.payment.default');
        $app = Factory::payment($wx_config);
        $ok = false;
        $response = $app->handlePaidNotify(function($message, $fail) use ($app, $ok) {

            Log::debug('notify', [$message]);

            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Trade::where('id', $message['out_trade_no'])->first();
            if (!$order || $order->payment_at) { // 如果订单不存在 或者 订单已经支付过了
                $ok = true;
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

            Log::debug('return_code', [$message['return_code']]);

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                $this->processTrade($order);
            }
            else {
                return $fail('通信失败，请稍后再通知我');
            }

            $order->save(); // 保存订单

            $ok = true;
            return true; // 返回处理完成
        });
//        Log::info($response->getContent());

        Log::debug(\Illuminate\Support\Facades\Request::getRequestUri());
        Log::debug('response', [$response]);
        Log::debug('is_ok', [$ok]);

        return 'SUCCESS';
    }

    /*
     * 微信支付完成后的同步跳转地址
     * */
    public function returnUrl(Request $request)
    {
        Log::debug('begin');

        //找到订单信息
        $trade = Trade::where('id', $request->input('trade_id', -1))
            ->where('user_openid', $this->openId())->first();
        if (!$trade)
        {
            Log::debug('order not exists');
            return view('wechat.error')->withErrors(['订单信息不存在']);
        }

        //防止一个用户查询不属于自己的订单信息
        if ($trade->user_openid !== $this->openId())
        {
            Log::debug('user openid error', ['trade_user_openid' => $trade->user_openid, 'user_openid' => $this->openId()]);
            return view('wechat.error')->withErrors(['用户ID错误']);
        }

        //处理订单
        $this->processTrade($trade);

        Log::debug('end');

        $device = Device::find($trade->device_id);

        //如果有会员卡，跳转到会员卡支付界面去
        $card = GetUserVipCard($this->openid());
        Log::debug('card', ['card' => $card]);
        if (VipCardIsActive($device, $card))
        {
            return redirect()->route('wechat.showCardInfo', [
                'device_id' => $device['id'],
                'card_id' => $card['id']
            ]);
        }

        return view('wechat.success', compact('device', 'trade'));
    }

    /*
     * 微信退款流程
     * */
    public static function refundMoney(Trade $trade, $money, $reason)
    {
        //检查是不是支付宝支付的记录
        if ($trade['payment_type'] !== Trade::PaymentType_WeChat) {
            Log::error('payment type error');
            flash()->warning('支付类型错误');
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
            flash()->warning('退款状态错误');
            return;
        }

        //检查金额是否正确
        if ($money <= 0) {
            Log::error('refund money error');
            flash()->warning('退款金额错误1');
            return;
        }

        //检查金额是否正确
        if ($money > $trade['payment_money']) {
            Log::error('refund money error');
            flash()->warning('退款金额错误2');
            return;
        }

        //向微信服务器核对一下订单信息

        //先保存为正在处理中
        $trade['refund_operator_id'] = Auth::user()->id;
        $trade['refund_id'] = IdGenerator::refundId();
        $trade['refund_status'] = Trade::RefundStatus_Processing;
        $trade['refund_money'] = $money;
        $trade->updateInfo();
        $trade->save();

        //向微信提交退款申请
        $wx_config = config('wechat.payment.default');
        $wx_config['notify_url'] = route('wechat.refund_notify');
        $app = Factory::payment($wx_config);
        $app->refund->byTransactionId($trade['payment_trade_id'], $trade['refund_id'], $trade['payment_money'], $money, [
            'refund_desc' => $reason
        ]);

        //在后台服务中隔一段时间更新一下退款状态。
        $trade['refund_status'] = Trade::RefundStatus_Success;
        $trade->updateInfo();
        $trade->save();
    }

//    更新退款状态
    public function refundNotify(Request $request)
    {
        $this->dispatch(new RefundMoneyJob());
        return 'SUCCESS';
    }

    /*
     * 查询设备是否在线
     * @return bool
     * */
    public function isOnline($device) : bool
    {
        $iot = new IotDevice($device['id']);
        $device['is_online'] = $iot->isOnline();
        $device->save();
        return $device['is_online'];
    }

    /*
     * 获取微信用户的openid
     * @return string
     * */
    public function openId() : string
    {
        $user = session('wechat.oauth_user.default'); // 拿到授权用户资料
        Log::debug('wechat user', ['user' => $user] );
        return $user ? $user['id'] : str_random(64);
    }

    /*
     * 获取微信的access_token
     * @return string
     * */
    public function getAccessToken()
    {
        $config = config('wechat.official_account.default');
        $app = Factory::officialAccount($config);
        $access_token = $app->access_token->getToken();
        return $access_token;
    }

    /*
     * 向微信服务器下单
     * */
    public function sendOrder(Trade $trade)
    {
        $result = $this->prepay($trade);

        $config = config('wechat.payment.default');
        $app = Factory::payment($config);
        $pay_config = $app->jssdk->sdkConfig($result['prepay_id']);
        $bridgeConfig = $app->jssdk->bridgeConfig($result['prepay_id']);

        $config = config('wechat.official_account.default');
        $app = Factory::officialAccount($config);
        $wx_config = $app->jssdk->buildConfig(['chooseWXPay', 'scanQRCode', 'getLocation'], config('app.debug'));

        Log::debug('pay_config', $pay_config);
        Log::debug('wx_config', $wx_config);

        return view('wechat.payment', compact('wx_config', 'pay_config', 'bridgeConfig'));
    }

    /*
     * 统一下单流程
     * @return string
     * */
    public function prepay(Trade $trade) : string
    {
        //统一下单
        $pay_config = config('wechat.payment.default');
        $app = Factory::payment($pay_config);
        $order = [
            'body' => $trade['goods_name'],
            'out_trade_no' => $trade['id'],
            'total_fee' => $trade['goods_price'],
            'notify_url' => 'https://v2.awoara.com/wechat/notify/'.$trade['id'], // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI',
            'openid' => $this->openId(),
        ];
        $result = $app->order->unify($order);

        Log::debug($order);
        Log::debug($result);

        return array_key_exists('prepay_id', $result) ? $result['prepay_id'] : '';
    }

    /*
     * 创建订单
     * @return App\Models\Trade
     * */
    public function createTrade() : Trade
    {
        $trade = new Trade();

        $trade['id'] = IdGenerator::tradeId();
        $trade['washcar_id'] = null;

        $trade['payment_type'] = Trade::PaymentType_WeChat;
        $trade['payment_status'] = Trade::PaymentStatus_Processing;
        $trade['confirm_status'] = Trade::GoodsStatus_None;
        $trade['refund_status'] = Trade::RefundStatus_None;
        $trade['withdraw_status'] = Trade::WithdrawStatus_None;

        return $trade;
    }

    /*
     * 保存设备的信息
     * */
    public function saveDeviceInfo(Trade $trade, Device $device)
    {
        $trade['owner_id'] = $device['owner_id'];
        $trade['device_id'] = $device['id'];
        $trade['is_self'] = $device['is_self'];
        $trade['platform_fee_rate'] = $device['platform_fee_rate'];
    }

    /*
     * 保存商品的信息
     * */
    public function saveGoodsInfo(Trade $trade, Goods $goods)
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

    /*
     * 保存下单用户的的个人信息
     * */
    public function saveUserInfo(Trade $trade, $user)
    {
        if ($user)
        {
            $trade['user_phone'] = $user['phone'];
            $trade['user_email'] = $user['email'];
        }
        $trade['user_ip'] = get_client_ip();
        $trade['user_openid'] = $this->openId();
    }

    public function processTrade(Trade $trade) : bool
    {
        //已经支付成功的记录
        if ($trade['payment_status'] === Trade::PaymentStatus_Success)
        {
            Log::debug('order processed', [$trade]);
            return true;
        }

        //安全考虑，这里再次从微信服务器查询一下支付记录，防止别人伪造支付记录
        $config = config('wechat.payment.default');
        $app = Factory::payment($config);
        $wxTrade = $app->order->queryByOutTradeNumber(''.$trade->id);

        try {
            \DB::beginTransaction();

            //通信成功并且已经支付成功
            if (($wxTrade['return_code'] === 'SUCCESS')
                && ($wxTrade['result_code'] === 'SUCCESS')
                && ($wxTrade['trade_state'] === 'SUCCESS'))
            {
                //检查APPID是否正确
                if ($wxTrade['appid'] !== $config['app_id'])
                {
                    Log::warning('appid error', [$wxTrade, $config]);
                    return false;
                }
                //检查商户ID是否正确
                if ($wxTrade['mch_id'] !== $config['mch_id'])
                {
                    Log::warning('mch_id error', [$wxTrade, $config]);
                    return false;
                }
                //检查支付金额是否正确
                if ((int)($wxTrade['total_fee']) !== $trade['goods_price'])
                {
                    Log::warning('total_fee error', [$wxTrade, $trade]);
                    return false;
                }
                //检查用户的openid
                if ($wxTrade['openid'] !== $trade['user_openid'])
                {
                    Log::warning('openid error', [$wxTrade, $trade]);
                    return false;
                }

                //检查支付状态
                if ($trade['payment_status'] !== Trade::PaymentStatus_Success)
                {
                    $trade['payment_trade_id'] = $wxTrade['transaction_id'];
                    $trade['payment_money'] = $wxTrade['total_fee'];
                    $trade['payment_at'] = Carbon::createFromFormat('YmdHis', $wxTrade['time_end']);
                    $trade['payment_status'] = Trade::PaymentStatus_Success;
                    $trade->paySign();

                    //保存月卡信息
                    $card = $this->saveVipCardInfo($trade);

                    //发送订单给设备
                    $this->sendOrderToDevice($trade, $card);

                }
            }
            else
            {
                $trade['payment_status'] = Trade::PaymentStatus_Failed;
            }

            $trade->save();

            DB::commit();
        }
        catch (\Exception $exception)
        {
            DB::rollBack();
            Log::error($exception, [ $trade, $wxTrade ]);
        }

        Log::debug('order process', [$trade]);
        return (Trade::PaymentStatus_Success === $trade['payment_status']);
    }

    /*
     * 发送记录给设备。
     * */
    public function sendOrderToDevice(Trade $trade, UserVipCard $card)
    {
        Log::debug('send order begin');

//        $this->dispatch(new PushMessageTask($trade->id));

        //已经推送成功了。
        if (Trade::GoodsStatus_Confirmed === $trade['confirm_status'])
        {
            Log::debug('order confirmed');
            return;
        }

//        if ($trade->goods_count > 1)
//        {
//            Log::debug('goods_count', ['trade' => $trade]);
//            return;
//        }

        //订单在２分钟以内
        if (Carbon::now()->diffInSeconds( $trade['payment_at']) >= 120)
        {
            Log::debug('order timeout', [$trade, Carbon::now()]);
            return;
        }

        $iotDevice = new IotDevice($trade['device_id']);
        if ( $iotDevice->netOrder($trade['id'], $trade['goods_seconds']) )
        {
            $trade['confirm_status'] = Trade::GoodsStatus_Confirmed;
            $trade['confirmed_at'] = Carbon::now();
            $trade->save();

            $card->used_count++;
            $card->save();
        }

        Log::debug('send order finished');
    }

    /*
     * 如果是可以多次使用的商品，那么保存为用户的会员卡记录。
     * */
    public function saveVipCardInfo(Trade $trade)
    {
        $trade['confirm_status'] = Trade::GoodsStatus_Confirmed;
        $trade['confirmed_at'] = Carbon::now();
        $trade->save();

        $goods = Goods::where('id', $trade['goods_id'])->withTrashed()->first();

        $card = new UserVipCard();
        $card['id'] = IdGenerator::vipCardId();
        $card['user_id'] = Auth::user() ? Auth::user()->id : null;
        $card['owner_id'] = $trade['owner_id'];
        $card['device_id'] = $trade['device_id'];
        $card['trade_id'] = $trade['id'];
        $card['user_openid'] = $trade['user_openid'];

        $days = isset($goods) ? (int)$goods->today_limit : 3;
        $card['seconds'] = $trade['goods_seconds'];
        $card['days'] = $trade['goods_days'];
        $card['goods_name'] = $trade['goods_name'];
        $card['used_count'] = 0;
        $card['total_count'] = $trade['goods_count'];
        $card['today_limit'] = $days > 3 ? $days : 3;
        $card['expiration'] = Carbon::now()->addDays($card['days']);
        $card->save();

        return $card;
    }
}

