<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ForceResetPasswordController extends Controller
{
    /*
     * 显示设置用户密码界面
     * */
    public function show(Request $request)
    {
        if (Auth::user()->hasRole('superadmin')) {
            $user_id = $request->input('user_id');
            $user = User::findOrFail($user_id);
            return view('admin.user.force_reset_password', compact('user'));
        }
        else
        {
            die(403);
        }
    }

    /*
     * 设置用户密码
     * */
    public function update(Request $request)
    {
        if (Auth::user()->hasRole('superadmin')) {
            $user_id = $request->input('user_id');
            $user = User::findOrFail($user_id);
            $user->password = bcrypt($request->input('password'));
            $user->api_token = str_random(60);
            $user->saveOrFail();
            flash()->success('密码重置成功！');
            return redirect()->route('user.index');
        }
        else {
            die(403);
        }
    }
}
