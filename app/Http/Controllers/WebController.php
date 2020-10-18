<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;

class WebController extends Controller
{
    public function index()
    {
        return redirect('http://www.awoara.com/');
    }

    public function showUserAgent()
    {
        var_dump(Agent::getUserAgent());
        return 'hello';
    }
}
