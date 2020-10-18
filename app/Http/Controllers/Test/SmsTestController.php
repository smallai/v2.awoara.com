<?php

namespace App\Http\Controllers\Test;

use App\Jobs\SendVerificationCodeJob;
use Carbon\Carbon;
use DeviceDetector\Parser\Client\Browser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Facades\Agent;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Strategies\OrderStrategy;

class SmsTestController extends Controller
{
    public function index(Request $request)
    {
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
        $this->dispatch(new SendVerificationCodeJob( $config));
        return 'hello';
    }

    public function index2()
    {
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'aliyun',
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'aliyun' => [
                    'access_key_id' => config('aliyun_sms.access_key_id'),
                    'access_key_secret' => config('aliyun_sms.access_secret'),
                    'sign_name' => config('aliyun_sms.sign_name'),
                ],
            ],
        ];

        try
        {
            $sms = new EasySms($config);
            $sms->send('18571559495', [
                'template' => config('aliyun_sms.template_auth'),
                'data' => [
                    'code' => mt_rand(100000, 999999),
                ],
            ]);
        }
        catch (\Exception $exception)
        {
            debug_backtrace();
        }
    }
}
