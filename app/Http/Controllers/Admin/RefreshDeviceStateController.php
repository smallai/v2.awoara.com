<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Jobs\RefreshIotDeviceStateJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RefreshDeviceStateController extends Controller
{
    public function update(Request $request)
    {
        $devices = $this->getDeviceIdList();
        $this->dispatch(new RefreshIotDeviceStateJob($devices));
        flash()->success('正在刷新设备状态...');
        return redirect()->route('dashboard.index');
    }

    private function getDeviceIdList() : array
    {
        $devices = Device::local()->get()->pluck('id')->toArray();
        return $devices;
    }
}
