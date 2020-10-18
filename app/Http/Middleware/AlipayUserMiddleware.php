<?php

namespace App\Http\Middleware;

use Closure;
use Overtrue\Socialite\User as SocialiteUser;

class AlipayUserMiddleware
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
            'id' => 'alipay_id', //openid
            'name' => 'alipay_test', //nickname
            'nickname' => 'abc_nickname', //nickname
            'avatar' => 'abc_avatar', //avatar
            'email' => null,
            'original' => [],
            'provider' => 'Alipay',
        ]);

        session(['alipay.oauth_user.default' => $user]);

        return $next($request);
    }
}
