<?php

namespace App\Http\Controllers\Alipay;

use App\Models\Device;
use \App\Utils\IdGenerator;
use App\Utils\IotDevice;
use App\Models\Trade;
use App\Models\UserVipCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class VipCardController extends Controller
{
    /*
     * 显示商品信息
     * */
    public function showCardInfo(Request $request)
    {
        $device_id = $request->input('device_id', -1);
        $card_id = $request->input('card_id', -1);

        //获取设备信息
        $device = Device::find($device_id);
        if (!$device)
        {
            return view('alipay.error')->withErrors(['无设备信息，请重试!编号: '.$device_id]);
        }

        //获取会员卡信息
        $card = UserVipCard::find($card_id);
        if (!$card)
        {
            return view('alipay.error')->withErrors(['无会员卡信息，请重试!卡号: '.$card_id]);
        }

        //检查会员卡是否可以在本机使用
        if ($device['owner_id'] !== $card['owner_id'])
        {
            return view('alipay.error')->withErrors(['会员卡不适用于该网点，请付费使用！']);
        }

        //检查用户的openid
        if ($card['user_openid'] !== $this->openid())
        {
            return view('alipay.error')->withErrors(['用户身份错误，请重试！']);
        }

        //检查可用次数
        if ($card['used_count'] >= $card['total_count'])
        {
            return view('alipay.error')->withErrors(['会员卡可用次数已用完，请重新购买！']);
        }

        //检查会员卡的有效期
        if ($card->leftTime() <= 0)
        {
            return view('alipay.error')->withErrors(['会员卡已过期，请重新购买！']);
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

        //２０１８－０９－１４　由于回调的时候，是支付宝回调的，所以需要提前保存用户的IP
        //保存扫码的时候的用户ip
        Session::put('user_ip', $request->getClientIp());

        $token = (string)Str::uuid();
        session()->push('token', $token);
        return view('alipay.showCardInfo', compact('device', 'card', 'token'));
    }

    /*
     * 开机
     * */
    public function carWash(Request $request)
    {
        $device_id = $request->input('device_id', -1);
        $card_id = $request->input('card_id', -1);

        $token = $request->input('token');
        $data = session()->pull('token');
        $oldToken = $data ? $data[0] : null;
        if ($oldToken !== $token) {
            Log::debug('token miss match', ['token' => $token, 'oldToken' => $oldToken]);
            return redirect()->route('alipay.error')->withErrors(['请勿重复提交！']);
        }
        //获取设备信息
        $device = Device::find($device_id);
        if (!$device)
        {
            return view('alipay.error')->withErrors(['无设备信息，请重试!编号: '.$device_id]);
        }

        //获取会员卡信息
        $card = UserVipCard::find($card_id);
        if (!$card)
        {
            return view('alipay.error')->withErrors(['无会员卡信息，请重试!卡号: '.$card_id]);
        }

        //检查会员卡是否可以在本机使用
        if ($device['owner_id'] !== $card['owner_id'])
        {
            return view('alipay.error')->withErrors(['会员卡不适用于该网点，请付费使用！']);
        }

        //检查用户的openid
        if ($card['user_openid'] !== $this->openid())
        {
            return view('alipay.error')->withErrors(['用户身份错误，请重试！']);
        }

        //检查可用次数
        if ($card['used_count'] >= $card['total_count'])
        {
            return view('alipay.error')->withErrors(['会员卡可用次数已用完，请重新购买！']);
        }

        //检查会员卡的有效期
        if ($card->leftTime() <= -60)
        {
            return view('alipay.error')->withErrors(['会员卡已过期，请重新购买！']);
        }

        $tradeId = IdGenerator::tradeId();

        if ($this->sendOrder($device->id, $tradeId, $card->seconds))
        {
            $trade = $this->saveRecord($device, $tradeId, $card);
            return redirect()->route('alipay.success', [
                'device_id' => $device->id,
                'trade_id' => $trade->id,
                'card_id' => $card->id,
            ]);
        }
        else
        {
            return view('alipay.showCardInfo', compact('device', 'card'))
                ->withErrors(['系统忙碌，请稍后重试！']);
        }
    }

    /*
     * 开机成功
     * */
    public function success(Request $request)
    {
//        dd($request->all());

        //设备信息
        $device = Device::find($request->input('device_id', -1));
        if (!$device)
        {
            return view('alipay.error')->withErrors(['无设备信息!']);
        }

        //订单信息
        $trade = Trade::find($request->input('trade_id', -1));
        if (!$trade)
        {
            return view('alipay.error')->withErrors(['无订单信息']);
        }

        //会员卡信息
        $card = UserVipCard::find($request->input('card_id', -1));
        if (!$card)
        {
            return view('alipay.error')->withErrors(['无会员开信息']);
        }

        //检查会员卡的有效期,如果过期时间在60秒以内，允许使用
        if ($card->leftTime() <= -60)
        {
            return view('alipay.error')->withErrors(['会员卡已过期，请重新购买！']);
        }

        //这里检查一下用户，是否就是创建记录的人。
        if ($trade['user_openid'] !== $this->openId())
        {
            return view('alipay.error')->withErrors(['用户身份错误']);
        }

        return view('alipay.success', compact('device', 'trade', 'card'));
    }

    public function error()
    {
        return view('alipay.error');
    }

    protected function openid() : string
    {
        $user = Session::get('alipay.oauth_user.default'); // 拿到授权用户资料
        Log::debug('alipay user', ['user' => $user] );
        return $user ? $user['id'] : str_random(64);
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
    public function saveRecord($device, $trade_id, $card) : Trade
    {
        $request = new Request();
        $trade = new Trade();

        try
        {
            DB::beginTransaction();

            //记录会员卡的使用次数
            $card->used_count++;
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

            $trade['user_ip'] = Session::get('user_ip');
            $trade['user_openid'] = $this->openId();

            $trade['owner_id'] = $device['owner_id'];
            $trade['device_id'] = $device['id'];
            $trade['is_self'] = $device['is_self'];

            $trade['goods_name'] = $card['goods_name'];
            $trade['goods_seconds'] = $card['seconds'];

            $trade['card_id'] = $card['id'];
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
            dd($exception);
        }

        return $trade;
    }
}
