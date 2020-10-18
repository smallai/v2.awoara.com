<?php

namespace App\Http\Controllers\Admin;

use App\Models\LogUserLogin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Facades\Agent;

class LoginController extends Controller
{
    //显示登录界面
    public function index()
    {
        Auth::logout();
        return view('admin.auth.login');
    }

    //用户登录
    public function login(Request $request)
    {
        $filed = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        Log::debug($request);
        $request->merge([$filed => $request->input('login')]);
        Log::debug($request);

        if ($filed === 'phone')
        {
            $loginSuccess = $this->loginByPhone($request);
        }
        else
        {
            $loginSuccess = $this->loginByEmail($request);
        }

        if ($loginSuccess)
        {
            $user = Auth::user();

            LogUserLogin::create([
                'user_id' => $user->id,
                'ip' => $request->getClientIp(),
                'src' => 'web',
                'login_at' => Carbon::now(),
                'browser' => Agent::browser(),
                'device' => Agent::device(),
                'platform' => Agent::platform(),
            ])->save();

            return redirect()->route('dashboard.index');
        }
        else
        {
            Log::error('user name or password error');
            flash()->error('邮箱或者密码错误');
            return redirect()->route('admin.login');
        }
    }

    //使用邮箱和密码登录
    protected function loginByEmail($request)
    {
        $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required|string:min:6|max:16'
            ], [
            'email.required' => '邮箱必须输入',
            'email.email' => '邮箱不正确',
            'password.required' => '密码必须输入',
            'password.min' => '密码长度不正确',
            'password.max' => '密码长度不正确',
        ]);

        $input = $request->all();
        return Auth::guard()->attempt([
            'email' => $input['email'],
            'password' => $input['password'],
        ], $request->input('remember', false));
    }

    //使用手机号和密码登录
    protected function loginByPhone($request)
    {
        $this->validate($request, [
            'phone' => [
                'required',
                'regex:/^1([358][0-9]|4[579]|66|7[0135678]|9[89])[0-9]{8}$/',
            ],
            'password' => 'required|string:min:6|max:16'
        ], [
            'phone.required' => '手机号码必须输入',
            'phone.regex' => '手机号码格式错误',
            'password.required' => '密码长度不正确',
            'password.min' => '密码长度不正确',
            'password.max' => '密码长度不正确',
        ]);

        $input = $request->all();
        return Auth::guard()->attempt([
            'phone' => $input['phone'],
            'password' => $input['password'],
        ], $request->input('remember', false));
    }

//    根据扫码信息登录
    protected function loginByWeChat()
    {
        return false;
    }

    public function logout()
    {
        if (!is_null(Auth::user()))
        {
            Auth::logout();
            flash('退出成功！')->success();
        }
        return redirect()->route('admin.login');
    }
}
