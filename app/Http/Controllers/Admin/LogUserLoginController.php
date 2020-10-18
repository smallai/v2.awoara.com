<?php

namespace App\Http\Controllers\Admin;

use App\Models\LogUserLogin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogUserLoginController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $user_id = $request->route('user_id');
        if (!isset($user_id))
            $user_id = $user->id;
        if (!$user->hasRole('superadmin'))
        {
            if ($user->id != $user_id)
                abort(403);
        }
        $logs = LogUserLogin::local()->withTrashed()->where('user_id', $user_id)->latest()->paginate(20);
        return view('admin.log_user_login.index', compact('logs'));
    }
}
