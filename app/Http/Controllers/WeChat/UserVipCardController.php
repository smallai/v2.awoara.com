<?php

namespace App\Http\Controllers\WeChat;

use App\Models\Device;
use \App\Utils\IdGenerator;
use App\Utils\IotDevice;
use App\Models\Trade;
use App\Models\UserVipCard;
use Carbon\Carbon;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class UserVipCardController extends Controller
{
    /*
     * 显示会员卡信息
     * */
    public function showCardInfo(Request $request)
    {
        $this->getAccessToken();

        $device_id = $request->input('device_id', -1);
        $card_id = $request->input('card_id', -1);

        //获取设备信息
        $device = Device::find($device_id);
        if (!$device)
        {
            return view('wechat.error')->withErrors(['无设备信息，请重试!编号: '.$device_id]);
        }

        //是否已停用
        if (Device::DeviceStatus_Disable === $device['status'])
        {
            return view('wechat.error')->withErrors(['设备维护，请稍后重试！']);
        }

        //检查是否在线
        if (!$this->isOnline($device))
        {
            return view('wechat.error')->withErrors(['设备离线,请稍后重试！']);
        }

        //获取会员卡信息
        $card = UserVipCard::find($card_id);
        if (!$card)
        {
            return view('wechat.error')->withErrors(['无会员卡信息，请重试!卡号: '.$card_id]);
        }

        //检查用户的openid
        if ($card['user_openid'] !== $this->openid())
        {
            return view('wechat.error')->withErrors(['用户身份错误，请重试！']);
        }

        //检查会员卡是否可以在本机使用
        if ($device['owner_id'] !== $card['owner_id'])
        {
            return view('wechat.error')->withErrors(['会员卡不适用于该网点，请付费使用！']);
        }

        //检查可用次数
        if ($card['used_count'] >= $card['total_count'])
        {
            return view('wechat.error')->withErrors(['会员卡可用次数已用完，请重新购买！']);
        }

        //检查会员卡的有效期
        if ($card->leftTime() <= 0)
        {
            return view('wechat.error')->withErrors(['会员卡已过期，请重新购买！']);
        }

        $today_count = Trade::whereDate('created_at', (string)Carbon::today())
            ->where('user_openid', '=', $this->openId())
            ->where('card_id', '=', $card->id)
            ->where('payment_type', Trade::PaymentType_VipCard)
            ->count();

        if ($today_count >= $card->today_limit)
        {
            return view('alipay.error')->withErrors(['会员卡当天可用次数已用完，请购买单次使用的套餐！']);
        }

        return view('wechat.cardInfo', compact('card', 'device'));
    }

    /*
     * 开机
     * */
    public function carWash(Request $request)
    {
        $card_id = $request->input('card_id', -1);
        $device_id = $request->input('device_id', -1);

        $device = Device::find($device_id);
        if (!$device)
        {
            return redirect()->route('wechat.error')->withErrors(['无设备信息，请重试!编号: '.$device_id]);
        }

        $card = UserVipCard::find($card_id);
        if (!$card)
        {
            return redirect()->route('wechat.error')->withErrors(['无会员卡信息，请重试!卡号: '.$card_id]);
        }

        //检查会员卡的所有人是否是当前用户
        if ($card['user_openid'] !== $this->openId())
        {
            return redirect()->route('wechat.error')->withErrors(['用户身份错误，请重试！']);
        }

        //检查会员卡是否可以在本机使用
        if ($device['owner_id'] !== $card['owner_id'])
        {
            return redirect()->route('wechat.error')->withErrors(['会员卡不适用于该网点，请购买单次洗车套餐！']);
        }

        //检查会员卡的可用次数
        if ($card['used_count'] >= $card['total_count'])
        {
            return redirect()->route('wechat.error')->withErrors(['会员卡可用次数已用完，请重新购买！']);
        }

        //检查会员卡的有效期,如果过期时间在60秒以内，允许使用
        if ($card->leftTime() <= -60)
        {
            return view('wechat.error')->withErrors(['会员卡已过期，请重新购买！']);
        }

        $trade_id = IdGenerator::tradeId();

        if ($this->sendOrder($device->id, $trade_id, $card->seconds))
        {
            $trade = $this->saveRecord($device, $trade_id, $card);
            return redirect()->route('wechat.success', [
                'device_id' => $device->id,
                'trade_id' => $trade->id,
                'card_id' => $card->id,
            ]);
        }
        else
        {
            return redirect()->route('wechat.showCardInfo', [
                'device_id' => $device->id,
                'card_id' => $card->id,
            ])->withErrors(['网络故障，请稍后重试！']);
        }
    }

    /*
     * 开机成功
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     * */
    public function success(Request $request)
    {
        //设备信息
        $device = Device::findOrFail($request->input('device_id', -1));
        if (!$device)
        {
            return redirect()->route('wechat.error')->withErrors(['无设备信息!']);
        }

        //订单信息
        $trade = Trade::findOrFail($request->input('trade_id', -1));
        if (!$trade)
        {
            return redirect()->route('wechat.error')->withErrors(['无订单信息']);
        }

        //会员卡信息
        $card = UserVipCard::findOrFail($request->input('card_id', -1));
        if (!$card)
        {
            return redirect()->route('wechat.error')->withErrors(['无会员开信息']);
        }

        //检查用户身份
        if ($card['user_openid'] !== $this->openid())
        {
            return view('wechat.error')->withErrors(['用户身份错误']);
        }

        //这里检查一下用户，是否就是创建记录的人。
        if ($trade['user_openid'] !== $this->openId())
        {
            return view('wechat.error')->withErrors(['用户身份错误']);
        }

        return view('wechat.success', compact('device', 'trade', 'card'));
    }

    public function error(Request $request)
    {
        return view('wechat.error');
    }

    /*
 * 查询设备是否在线
 * @return bool
 * */
    public function isOnline($device) : bool
    {
        $iot = new IotDevice($device['id']);
        $device->is_online = $iot->isOnline();
        $device->save();
        return $device->is_online;
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
     * 发送订单给设备
     * */
    public function sendOrder($device_id, $trade_id, $seconds)
    {
        $iot = new IotDevice($device_id);
        return $iot->netOrder($trade_id, $seconds);
    }

    /*
     * 保存记录
     * */
    public function saveRecord($device, $trade_id, $card)
    {
        $request = new Request();
        $trade = new Trade();


        try
        {
            DB::beginTransaction();

            //记录会员卡的使用次数
            $card['used_count'] += 1;
            $card->save();

            $trade['id'] = $trade_id;
            $trade['payment_type'] = Trade::PaymentType_VipCard;
            $trade['payment_status'] = Trade::PaymentStatus_Success;
            $trade['confirm_status'] = Trade::GoodsStatus_Confirmed;
            $trade['refund_status'] = Trade::RefundStatus_None;
            $trade['withdraw_status'] = Trade::WithdrawState_Disable;

            $trade['payment_money'] = 0;
            $trade['refund_money'] = 0;
            $trade['withdraw_money'] = 0;
            $trade['platform_money'] = 0;

            $trade['user_ip'] = $request->getClientIp();
            $trade['user_openid'] = $this->openId();

            $trade['owner_id'] = $device->owner_id;
            $trade['device_id'] = $device->id;
            $trade['is_self'] = $device->is_self;

            $trade['goods_name'] = $card->goods_name;
            $trade['goods_seconds'] = $card->seconds;

            $trade['card_id'] = $card->id;
            $trade['platform_fee_rate'] = 1000;
            $trade['confirm_status'] = Trade::GoodsStatus_Confirmed;
            $trade['confirmed_at'] = Carbon::now();
            $trade->save();

            DB::commit();

            Log::debug('save trade info', [$trade]);
        }
        catch (\Exception $exception)
        {
            DB::rollBack();
            \Log::debug('exception', [$exception]);
        }

        return $trade;
    }
}
