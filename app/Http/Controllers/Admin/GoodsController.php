<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Models\Goods;
use App\Policies\GoodsPolicy;
use App\Scopes\DeviceScope;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GoodsController extends Controller
{
    protected function goods(Request $request)
    {
        $builder = Goods::local()->with('device', 'owner');
        if (is_numeric($request->input('device_id'))) {
            $builder->where('device_id', $request->input('device_id'));
        }

        $items =  $builder->orderBy('created_at', 'desc')->paginate();

        if (is_numeric($request->input('device_id'))) {
            $items->appends(['device_id' => $request->input('device_id')]);
        }

        return $items;
    }

    protected function devices()
    {
        $user_id = Auth::user()->id;
        debugbar()->log('user_id='.$user_id);
        return Device::local()->latest()->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = $this->goods($request);
        $device = null;
        if (is_numeric($request->input('device_id'))) {
            $device = Device::findOrFail($request->input('device_id'));
        }
        $devices = Device::local()->get();
        return view('admin.goods.index', compact('items', 'device', 'devices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $devices = $this->devices();
        return view('admin.goods.create', compact('devices'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $all = $request->all();
        debugbar()->debug($all);
        //        $user = Auth::user();
        $device = Device::local()->findOrFail($all['device_id']);
        $all['is_sale'] = $request->input('is_sale', false);
        $all['is_recommend'] = $request->input('is_recommend', false);
        $all['owner_id'] = $device->owner_id;
        debugbar()->debug($all);
        Goods::create($all);
        flash()->success('保存成功!');
//        return 'hello';
        return redirect()->route('goods.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Goods::local()->findOrFail($id);
        return view('admin.goods.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Goods::local()->withTrashed()->findOrFail($id);
        return view('admin.goods.edit', compact('item'));
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
        $item = Goods::local()->findOrFail($id);
        $item->fill($request->all());
        $item->saveOrFail();
        flash()->success('保存成功！');
        debugbar()->debug($request->all());
        return redirect()->route('goods.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Goods::local()->findOrFail($id);
        if ($item->delete())
            flash()->success('删除成功！');
        else
            flash()->warning('删除成功！');
        return redirect()->route('goods.index');
    }

    protected static function boot()
    {
        parent::boot();

//        static::addGlobalScope(new DeviceScope());
    }
}
