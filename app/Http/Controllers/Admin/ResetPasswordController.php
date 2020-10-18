<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
    /*
     * 显示重置用户密码界面
     * */
    public function show()
    {
        $id = Auth::id();
        return view('admin.user.reset_password', ['user_id' => $id]);
    }

    /*
     * 更新密码
     * */
    public function update(Request $request)
    {
        if (Auth::guard()->attempt([
            'id' => Auth::id(),
            'password' => $request->input('password'),
        ])) {
            $user = User::find(Auth::id());
            $user->password = bcrypt($request->input('password2'));
            $user->api_token = str_random(60);
            $user->save();
            flash()->success('密码已更新，请重新登录');
            Auth::logout();
            return redirect()->route('dashboard.index');
        } else {
            flash()->warning('请重试！');
            return redirect()->route('user.reset_password');
        }
    }
}
