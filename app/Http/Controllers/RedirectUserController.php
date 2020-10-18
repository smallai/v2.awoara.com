<?php

namespace App\Http\Controllers;

use App\Jobs\RefreshIotDeviceStateJob;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Jenssegers\Agent\Facades\Agent;

class RedirectUserController extends Controller
{
    //用户扫码后的入口地址
    public function entry(Request $request)
    {
        Log::debug('entry', [
            'request_url' => $request->fullUrl(),
            'request_data' => $request->all(),
        ]);

        //获取扫码的设备ID
        $device_id = $request->input('id', -1);
        if ($device_id < 0) {
            $device_id = $request->input('device_id', -1);
        }

        Session::put('device.id', $device_id);

        //刷新设备的网络状态
        dispatch(new RefreshIotDeviceStateJob($device_id));

//        dd(Agent::getUserAgent());

        //保存扫码的的用户信息
        $this->saveVisitorInfo($request);

        if (isWeChat()) {
            return redirect()->route('wechat.showGoods', ['device_id' => $device_id]);
        }

        if (isAlipay()) {
            return redirect()->route('alipay.showGoods', ['device_id' => $device_id]);
        }

        return redirect()->route('alipay.error')->withErrors(['请使用支付宝或微信扫码！']);
    }

    /*
     * 保存访问者信息
     * */
    public function saveVisitorInfo(Request $request)
    {
        Visitor::create([
            'ip' => $request->getClientIp(),
            'login_at' => Carbon::now(),
            'browser' => Agent::browser(),
            'device' => Agent::device(),
            'platform' => Agent::platform(),
            'type' => $this->getType(),
            'agent' => Str::limit($request->userAgent(), 100, '...'),
            'session_id' => $request->getSession()->getId(),
        ])->save();
    }

    /*
     * 获取客户端类型
     * */
    public function getType()
    {
        if (isAlipay()) {
            return 1;
        }

        if (isWeChat()) {
            return 2;
        }

        return 0;
    }
}
