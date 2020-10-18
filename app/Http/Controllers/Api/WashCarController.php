<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\WashCarCollection;
use App\Http\Resources\WashCarResource;
use App\WashCar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WashCarController extends Controller
{
    protected function user()
    {
        return Auth::guard('api')->user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = WashCar::with('device', 'owner', 'user', 'trade')->paginate($this->user()->page_size ?? 10);
        return new WashCarCollection($items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        throw new NotFoundHttpException();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        throw new NotFoundHttpException();
//        $item = WashCar::create($request->all());
//        return new WashCarResource($item);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
//        return response()->json([
//            'hello' => 'world'
//        ]);
        $item = WashCar::with('device', 'owner', 'user', 'trade')->findOrFail($id);
        return new WashCarResource($item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        throw new NotFoundHttpException();
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
        throw new NotFoundHttpException();
//        $item = WashCar::findOrFail($id);
//        $item->saveOrFail($request->all());
//        return new WashCarResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        throw new NotFoundHttpException();

//        $item = WashCar::findOrFail($id);
//        $item->delete();
//        return new WashCarResource($item);
    }
}
