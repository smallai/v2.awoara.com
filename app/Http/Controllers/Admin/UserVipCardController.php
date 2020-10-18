<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Models\UserVipCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserVipCardController extends Controller
{
    public function index(Request $request)
    {
        $device = null;
        if (is_numeric($request->input('device_id'))) {
            $device = Device::local()->findOrFail($request->input('device_id'));
            $items = UserVipCard::local()->where('device_id', $device->id)->orderBy('created_at', 'desc')->paginate();
        }
        else {
            $items = UserVipCard::local()->orderBy('created_at', 'desc')->paginate();
        }
        $devices = Device::local()->get();

        return view('admin.card.index', compact('items', 'devices', 'device'));
    }
}
