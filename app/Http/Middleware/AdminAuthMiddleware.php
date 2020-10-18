<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminAuthMiddleware
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
        $user = Auth::user();
        if (isset($user) && $user->hasRole('superadmin|admin'))
        {
            return $next($request);
        }
        Log::debug('redirect user by admin middleware');

        return redirect()->route('admin.login');
    }
}
