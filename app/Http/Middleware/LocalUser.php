<?php

namespace App\Http\Middleware;

use Closure;
use Overtrue\Socialite\User as SocialiteUser;

class LocalUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = new SocialiteUser([
            'id' => 'abc', //openid
            'name' => 'abc_name', //nickname
            'nickname' => 'abc_nickname', //nickname
            'avatar' => 'abc_avatar', //avatar
            'email' => null,
            'original' => [],
            'provider' => 'WeChat',
        ]);

        session(['wechat.oauth_user.default' => $user]);

        return $next($request);
    }
}
