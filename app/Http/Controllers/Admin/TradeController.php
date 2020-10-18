<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Http\Controllers\Alipay\AlipayController;
use App\Http\Controllers\WeChat\WeChatPayController;
use App\Models\Trade;
use App\Models\User;
use App\Models\UserVipCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class TradeController extends Controller
{
    protected function items(Request $request)
    {
        $builder = Trade::local()->with('device', 'owner');
        if (is_numeric($request->input('withdraw_id'))) {
            $builder->where('withdraw_id', $request->input('withdraw_id'));
        }

        //指定了记录类型
        if (is_numeric($request->input('type'))) {
            $builder->type($request->input('type'));
        }

//        if (isset($input['date'])) {
//            $builder->where('created_at', $request->input('date'));
//        }

        if (is_numeric($request->input('device_id'))) {
            $builder->where('device_id', $request->input('device_id'));
        }

        $items = $builder->orderBy('created_at', 'desc')->paginate();

        //提现记录
        if (is_numeric($request->input('withdraw_id'))) {
            $items->appends(['withdraw_id' => $request->input('withdraw_id')]);
        }

        //记录类型
        if (is_numeric($request->input('type'))) {
            $items->appends(['type' => $request->input('type')]);
        }

//        //记录类型
//        if (isset($input['date'])) {
//            $items->appends(['date' => $request->input('date')]);
//        }

        if (is_numeric($request->input('device_id'))) {
            $items->appends(['device_id' => $request->input('device_id')]);
        }

        return $items;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date_range = null;
        $input = \Illuminate\Support\Facades\Request::all();
        if (isset($input['date_range']))
        {
            $date_range = $input['date_range'];
        }
        else
        {
            $date_range = ''.Carbon::yesterday() .' - ' .Carbon::today();
        }
//        return 'hello';
//        debugbar()->debug($input);
//        debugbar()->debug(str_before($date_range, ' - '));
//        debugbar()->debug(str_after($date_range, ' - '));
        $items = $this->items($request);
//        \JavaScript::put([
//            'items' => $items
//        ]);
//        debugbar()->debug($items);
        $type = $request->input('type');
        $device = null;
        if (is_numeric($request->input('device_id'))) {
            $device = Device::findOrFail($request->input('device_id'));
        }
//        return 'hello';
        $devices = Device::local()->get();

//        debugbar()->debug($devices);
        return view('admin.trade.index', compact('items', 'date_range', 'device', 'devices', 'type'));
//        return $items;
    }

    public function data()
    {
        $items = $this->items();
        return $items;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort(403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Trade::local()->findOrFail($id);
        return view('admin.trade.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort(403);
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
        abort(403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(403);
    }

    public function showRefundMoney(Request $request)
    {
        if (!Auth::user()->hasRole('superadmin|admin')) {
            die(403);
        }

        $trade_id = $request->input('trade_id', -1);
        $trade = Trade::findOrFail($trade_id);
        $device = Device::findOrFail($trade['device_id']);
        return view('admin.trade.refund_money', compact('trade', 'device'));
    }

    public function refundMoney(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('superadmin|admin')) {
            die(403);
        }

        $trade_id = $request->input('trade_id', -1);
        $money = $request->input('money', 0);
        $reason = $request->input('reason', '');
        $trade = Trade::local()->findOrFail($trade_id);
        $money = to_int($money);

        Log::debug("refund", ['trade_id' => $trade_id,  'money' => $money, 'reason' => $reason]);
//        return 'xxx';

        if ($trade->isCanRefund()) {

            if ($request->has('delete_vip_card')) {
                $card = UserVipCard::where('trade_id', $trade_id)->first();
                if ($card) {
                    $card->delete();
                    Log::debug("delete_vip_card", [
                        'card_id' => $card->id,
                        'googs_name' => $card->goods_name,
                    ]);
                }
            }

            if (Trade::PaymentType_Alipay === $trade['payment_type']) {
                AlipayController::refundMoney($trade, $money, $reason);
            } else if (Trade::PaymentType_WeChat === $trade['payment_type']) {
               WeChatPayController::refundMoney($trade, $money, $reason);
            }
            else {

            }
        }

//        return 'aaa';
        return redirect()->route('trade.index');
    }
}
