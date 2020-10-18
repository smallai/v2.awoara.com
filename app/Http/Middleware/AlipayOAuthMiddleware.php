<?php

namespace App\Http\Middleware;

use Closure;
use DeviceDetector\Cache\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AlipayOAuthMiddleware
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
        if (!Session::get('alipay.oauth_user.default'))
        {
            Log::debug('redirect to alipay oauth url');
            return redirect()->route('alipay.oauth', ['target_url' => $request->fullUrl()] );
        }

        return $next($request);
    }
}
