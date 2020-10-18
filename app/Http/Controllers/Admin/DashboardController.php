<?php

namespace App\Http\Controllers\Admin;

use App\Banknote;
use App\Models\Device;
use App\Utils\IotDevice;
use App\Jobs\RefreshIotDeviceStateJob;
use App\Jobs\WithdrawMoneyJob;
use App\Policies\TradePolicy;
use App\Models\Trade;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Webpatser\Uuid\Uuid;

class DashboardController extends Controller
{
    protected function deviceKey($deviceId)
    {
        return 'orders_id_'.$deviceId;
    }

        /*
     * 查询设备是否在线
     * @return bool
     * */
    public function isOnline(Device $device) : bool
    {
        $iot = new IotDevice($device->id);
        $device['is_online'] = $iot->isOnline();
        $device->save();
        return $device['is_online'];
    }

    /*
     * 显示登陆后的主页
     * */
    public function index(Request $request)
    {
//        Log::debug('count', [ $this->carWashCount(Device::findOrFail(18061001), '2018-09-16') ]);
//        return 'abc';

//        $this->refresh();

        if (Auth::user()->hasRole('superadmin')) {
            $devices = Device::paginate();
        }
        else if (Auth::user()->hasRole('admin')) {
            $devices = Auth::user()->devices()->paginate();
        }

        foreach ($devices as $device)
        {
            $input = $request->all();
            $date = Carbon::today();
            if (isset($input['date']))
                $date = $input['date'];
            $trades = $device->trade()->canWithdrawMoney()->get();

//            $this->dispatch(new RefreshIotDeviceStateJob($device->id));

            $snap_uuid = Str::orderedUuid()->toString();
            $device['snap_uuid'] = $snap_uuid;
            $device['withdraw_money'] = $trades->sum('withdraw_money');
            $device['banknote_money'] = $device->trade()->money()->sum('withdraw_money');
            $device['qrcode_money']   = $device->trade()->qrcode()->sum('withdraw_money');
            $device['yesterday_washcar_count']   = $this->carWashCount($device, Carbon::yesterday());
            $device['today_washcar_count']   = $this->carWashCount($device, Carbon::today());

//            dd($device);

            Session::put(GetDeviceSnapKey($device->id), [
                'snap_uuid' => $snap_uuid,
                'device_id' => $device->id,
                'owner_id' => $device->owner_id,
                'withdraw_money' => $device->withdraw_money,
                'trades_id' => $trades->pluck('id')->all(),
            ]);
        }
//        $items = $devices->chunk(4);
//        debugbar()->debug($items);
        return view('admin.dashboard.index', compact('devices'));
    }

    public function refresh()
    {
        if (Auth::user()->hasRole('superadmin')) {
            $devices = Device::all('id')->pluck('id')->toArray();
//            dd($devices);
            $this->dispatch(new RefreshIotDeviceStateJob($devices));
        }
    }

    public function carWashCount($device, $date = null)
    {
        if (\is_null($date)) {
            $date = Carbon::today();
        }

        $count = Trade::where('device_id', $device['id'])
            ->whereDate('created_at', $date)
            ->where('payment_status', Trade::PaymentStatus_Success)
            ->count();
        return $count;
    }
}
