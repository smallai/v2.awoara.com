<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Utils\IotDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeviceConfigController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit(Request $request)
    {
        $device_id = $request->input('device_id');
        $device = Device::findOrFail($device_id);
        $config = (new App\Models\Trade($device_id))->readConfig();
        return view('admin.device.config', compact('device', 'config'));
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request)
    {
        $device_id = $request->input('device_id');
        $config = $request->except(['device_id']);
        $success = (new IotDevice($device_id))->writeConfig($config);
        if ($success) {
            flash()->success('修改成功！');
        } else {
          flash()->warning('设备离线或者忙碌，请稍后重试！');
        }
        return redirect()->action('Admin\DeviceConfigController@edit', ['device_id' => $device_id]);
    }
}
