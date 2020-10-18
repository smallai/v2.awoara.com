<?php

namespace App\Http\Controllers\Api;

use App\Jobs\SendVerificationCodeJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Facades\Agent;

class SendVerificationCodeController extends Controller
{
    public function send(Request $request)
    {
        return response()->json($request->all());

        $this->validate($request, [
            'phone' => [
                'required',
                'regex:/^1([358][0-9]|4[579]|66|7[0135678]|9[89])[0-9]{8}$/',
            ],
        ], [
            'phone.required' => '手机号码必须输入',
            'phone.regex' => '手机号码格式错误',
        ]);

        $config = [
            'device_id' =>  Session::get('device_id') ?? Cookie::get('device_id'),
            'client_ip' => $request->getClientIp(),
            'browser' => Agent::browser(),
            'device' => Agent::device(),
            'platform' => Agent::platform(),
            'phone' => $request->input('phone'),
            'code' => mt_rand(100000, 999999),
            'template' => config('aliyun_sms.template_auth'),
            'sign_name' => config('aliyun_sms.sign_name'),
            'expiration' => Carbon::now()->addMinutes(5),
        ];

        $this->dispatch(new SendVerificationCodeJob($config));

        return response()->json([
            'status' => 'success'
        ]);
    }
}
