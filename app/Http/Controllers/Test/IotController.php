<?php

namespace App\Http\Controllers\Test;

use App\Utils\IotDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IotController extends Controller
{
    public function ping(Request $request)
    {
        $iot = new IotDevice($request->input('device_id', -1));
        return $iot->ping() ? 'true' : 'false';
    }

    public function netOrder(Request $request)
    {
        $device_id = $request->input('device_id', 18011001);
        $order_id = $request->input('order_id', 100);
        $iot = new IotDevice($device_id);
        return $iot->netOrder($order_id, 10) ? 'true' : 'false';
    }

    public function orderFinish(Request $request)
    {
        $iot = new IotDevice($request->input('device_id', -1));
        return $iot->orderFinish() ? 'true' : 'false';
    }
}
