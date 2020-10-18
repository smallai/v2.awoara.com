<?php

namespace App\Http\Controllers\WeChat;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WeChatController extends Controller
{
    /*
     * @return string
     * */
    public function serve()
    {
        $config = config('wechat.official_account.default');
        $app = Factory::officialAccount($config);
        $app->server->push(function($message){
        });

        return $app->server->serve();
    }

    /*
     * @return null
     * */
    public function user()
    {
        $user = session('wechat.oauth_user'); // 拿到授权用户资料
        dd($user);
    }
}
