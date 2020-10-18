<?php

namespace App\Http\Controllers\Alipay;

use anerg\OAuth2\OAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class AlipayOAuthController extends Controller
{
    public function config($id)
    {
        //支付宝配置参数
//        $config = [
//            'app_id'      => env('ALIPAY_APP_ID'),
//            'scope'       => 'auth_base',
//            'pem_private' => '/data/dev.awoara.com/alipay_sandbox/app_private_key.pem', // 你的私钥
//            'pem_public'  => '/data/dev.awoara.com/alipay_sandbox/app_public_key.pem', // 支付宝公钥
//            'callback'    => [
//                'default' => route('alipay.callback'),
//                'mobile'  => route('alipay.callback'),
//            ],
//        ];

        $url = 'https://v2.awoara.com/alipay/oauth/callback';
//        $url = \Illuminate\Support\Facades\Request::getHost() + '/alipay/oauth/callback';
        $config = [
//            'app_id'      => env('ALIPAY_APP_ID'),
            'app_id'      => config('alipay.app_id'),
            'scope'       => 'auth_base',
            'pem_private' => '/data/v2.awoara.com/alipay/app_private_key.pem', // 你的私钥
            'pem_public'  => '/data/v2.awoara.com/alipay/app_public_key.pem', // 支付宝公钥
            'callback'    => [
                'default' => $url.'/'.$id,
                'mobile'  => $url.'/'.$id,
            ],
        ];
//        dd($config);

        return $config;
    }

    public function redirectUser(Request $request)
    {
//        dd($request->session()->getId());
        Log::debug('alipay oauth start');
        $key = str_random(64);
        $config = $this->config($key);
        $url = $request->input('target_url', route('alipay.showGoods', ['device_id' => Session::get('device.id')]));

        Cache::put($key, $url, 60);
        $url = Cache::get($key);
//        Log::debug('url', [$key, $url]);
//        return 'hello';

        Log::debug('alipay oauth start info', [
            'session_id' => \session()->getId(),
            'key' => $key,
            'url' => $url,
        ]);

        $OAuth  = OAuth::getInstance($config, 'alipay');
        $OAuth->setDisplay('mobile'); //此处为可选,若没有设置为mobile,则跳转的授权页面可能不适合手机浏览器访问
        $url = $OAuth->getAuthorizeURL();
        Log::debug('oauth', ['url' => $url]);
        return redirect($url);
    }

    public function callback($key)
    {
        $channel = 'alipay';
        $config = $this->config($key);
        $OAuth = OAuth::getInstance($config, $channel);
//        $OAuth->getAccessToken();
        /**
         * 在获取access_token的时候可以考虑忽略你传递的state参数
         * 此参数使用cookie保存并验证
         */
        $ignore_stat = true;
        $OAuth->getAccessToken(true);
        $sns_info = $OAuth->openid();
        /**
         * 此处获取了sns提供的用户数据
         * 你可以进行其他操作
         */
        Log::debug('user_info', [$sns_info]);

//        dd(session()->getId());

        Session::put('alipay.oauth_user.default', [
            'id' => $OAuth->openid()
        ]);
        $url = Cache::get($key, 'target_url');

        Log::debug('alipay oauth info', [
            'session_id' => \session()->getId(),
            'id' => $OAuth->openid(),
            'key' => $key,
            'url' => $url,
        ]);

//        dd($key);

        var_dump($url);
//        var_dump(Session::exists('alipay.oauth'));
        return redirect($url);
//        var_dump(Session::get('alipay.oauth'));
//        return 'hello';
    }
}
