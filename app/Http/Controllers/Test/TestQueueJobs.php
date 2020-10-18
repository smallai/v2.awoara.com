<?php

namespace App\Http\Controllers\Test;

use App\Jobs\DemoJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestQueueJobs extends Controller
{
    public function demoJob()
    {
//        dd(config('database.connections.mysql'));
//        dd(config('queue.default'));
//        dd(env('QUEUE_DRIVER'));
//       $result =  DemoJob::dispatch();
        $result = dispatch(new DemoJob())->delay(5);
        dd($result);
    }
}
