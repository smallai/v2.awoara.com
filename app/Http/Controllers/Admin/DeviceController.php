<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Models\Goods;
use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Models\UserVipCard;
use App\Policies\DevicePolicy;
use App\Scopes\DeviceScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    protected function devices()
    {
        $builder = Device::local()->with('owner');
        return $builder->latest()->paginate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = $this->devices();
        return view('admin.device.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->hasRole('superadmin'))
        {
            return view('admin.device.create');
        }
        else
        {
            die(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('superadmin'))
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'product_key' => 'required|min:1|max:64',
                'device_name' => 'required|min:1|max:64',
                'device_secret' => 'required|min:1|max:64',
                'platform_fee_rate' => 'required|integer|min:1|max:1000',
                'name' => 'max:64',
                'address' => 'max:128',
                'vip_card_today_limit' => 'min:1',
            ]);

            $validator->validate();

            $device = Device::firstOrCreate([
                ['id', $input['device_name']],
            ]);
            $device['id'] = intval($input['device_name']);
            $device['product_key'] = $input['product_key'];
            $device['device_name'] = $input['device_name'];
            $device['device_secret'] = $input['device_secret'];
            $device['platform_fee_rate'] = $input['platform_fee_rate'];
            $device['name'] = $input['name'];
            $device['address'] = $input['address'];
//            $device['vip_card_today_limit'] = $input['vip_card_today_limit'];
            $device->saveOrFail();

            return redirect()->route('device.index')->with('success', '创建成功！');
        }

        abort(Response::HTTP_FORBIDDEN);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Device::local()->findOrFail($id);
        return view('admin.device.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Device::local()->findOrFail($id);
        return view('admin.device.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $device = Device::local()->findOrFail($id);
        if (Auth::user()->hasRole('superadmin'))
            $device->fill($request->all());
        else
            $device->fill($request->only('name', 'address'));

        $device->save();
        flash()->success('修改成功！');
        return redirect()->route('device.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Device::local()->findOrFail($id);
        $item->delete();
        flash()->success('删除成功！');
        return redirect()->route('device.index');
    }

    /*
     * 显示管理员信息
     * */
    public function showSetAdmin(Request $request)
    {
        Log::debug('request', $request->all());

        if (!Auth::user()->hasRole('superadmin')) {
            die(403);
            return;
        }
        $device = Device::findOrFail($request->input('device_id', -1));
        $users = User::all();
        return view('admin.device.set_admin', compact('device', 'users'));
    }

    /*
     * 修改机器的管理员信息
     * */
    public function setAdmin(Request $request)
    {
        Log::debug('request', $request->all());

        if (!Auth::user()->hasRole('superadmin')) {
            die(403);
            return;
        }

        $device_id = $request->input('device_id', -1);
        $owner_id = $request->input('owner_id', -1);

        if (($device_id > 0) && ($owner_id > 0)) {
            $item = Device::local()->findOrFail($device_id);
            $item->owner_id = $request->input('owner_id');
            $item->save();
            Goods::where('device_id', $device_id)->update(['owner_id' => $owner_id]);
            UserVipCard::where('device_id', $device_id)->update(['owner_id' => $owner_id]);
//            Trade::where('device_id', $device_id)->update(['owner_id' => $owner_id]);

            flash()->success('修改成功！');
        }
        else
        {
            flash()->error('参数错误！');
        }

        return redirect()->route('device.index');
    }
}
