<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DeviceQrCodeController extends Controller
{
    public function show(Request $request)
    {
        $device_id = $request->input('device_id', -1);
        if ($device_id <= 0) {
            $device_id = $request->input('id', -1);
        }
        $path = self::qrcode($device_id);
        return view('admin.device.qrcode', compact('path'));
    }

    public static function qrcode($device_id) : string
    {
        $device = Device::findOrFail($device_id);
        $path = '/qrcode/device_'.$device['id'].'_'.$device['name'].'.png';

        //如果图片不存在就生成一个.
//        if (!file_exists(public_path($path))) {
            QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->encoding('utf-8')
                ->merge('/public/logo.png', 0.2)
                ->generate('http://v2.awoara.com/pay?id=' . (string)($device_id), public_path($path));
//                ->generate(route('redirect.user', ['id' => $device_id]), public_path($path));
//        }

        return $path;
    }
}
