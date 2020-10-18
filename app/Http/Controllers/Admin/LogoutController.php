<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function index()
    {
        if (!is_null(Auth::user()))
        {
            Auth::logout();
            flash('退出成功！')->success();
        }
        return redirect()->route('admin.login');
    }
}
