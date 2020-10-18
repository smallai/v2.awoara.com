<?php

namespace App\Jobs;

use App\Models\PhoneCode;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Strategies\OrderStrategy;

class SendVerificationCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $param;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($param)
    {
        $this->param = $param;
        if (!isset($this->param['code'])) {
            $this->param['code'] = mt_rand(100000, 999999);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->delete();

        try
        {
            //保存记录到数据库中
            $phoneCode = new PhoneCode([
                //登录信息
                'device_id' => $this->param['device_id'],   //用户扫码的设备ID
                'client_ip' => $this->param['client_ip'],   //用户的IP
                'browser' => $this->param['browser'],       //用户使用的浏览器
                'device' => $this->param['device'],         //用户的手机型号
                'platform' => $this->param['platform'],     //用户使用的系统

                //验证码信息
                'phone' => $this->param['phone'],           //手机号码
                'code' => $this->param['code'],             //验证码
                'template' => $this->param['template'],     //使用的模板
                'sign_name' => $this->param['sign_name'],   //使用的签名
                'expiration' => $this->param['expiration'], //到期时间
            ]);
            $phoneCode->saveOrFail();

            //加载配置信息
            $config = $this->getConfig();

            //发送短信
            $sms = new EasySms($config);
            $sms->send($phoneCode->phone, [
                'template' => $phoneCode->template,
                'data' => [
                    'code' => $phoneCode->code,
                ],
            ]);
        }
        catch (\Exception $exception)
        {
            Log::error($exception);
        }
    }

    public function getConfig()
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

        Log::debug($config);

        return $config;
    }
}
