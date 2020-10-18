<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Models\Trade;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CenterController extends Controller
{
    protected function recordDate($date, $device_id)
    {
        $builder = Trade::select(
            DB::raw('COUNT(*) AS payment_count'),
            DB::raw('COUNT(DISTINCT user_openid) AS washcar_count'),
            DB::raw('SUM(payment_money) AS payment_money'),
            DB::raw('SUM(refund_money) AS refund_money'),
            DB::raw('SUM(withdraw_money) AS withdraw_money'),
            DB::raw('SUM(platform_money) AS platform_money')
        )->local();

        if (!is_null($date))
            $builder->whereDate('created_at', $date);
        if (is_numeric($device_id))
            $builder->where('device_id', $device_id);

        $records = $builder->where('payment_status', Trade::PaymentStatus_Success)
            ->first();
//        ->whereIn('payment_type', [Trade::PaymentType_Alipay, Trade::PaymentType_WeChat])
        if (is_null($records['date']))
            $records['date'] = $date;
        return $records;
    }

    protected function records($month, $device_id)
    {
        //TODO: 重构这部分代码！！！
        Log::debug('month', [$month]);
        //计算这个月有多少天
        $now = Carbon::now();
        $curMonth = Carbon::createFromFormat('Y-m', $month);
        $days = $curMonth->daysInMonth;
        if ($now->format('Y-m') == $month)
            $days = $now->day;
        Log::debug($days);

        $begin = Carbon::create($curMonth->year, $curMonth->month, 1, 0, 0, 0);
        $end = Carbon::create($curMonth->year, $curMonth->month, $days+1, 0, 0, 0);
        Log::debug('daterange', [$begin->toString(), $end->toString()]);

        $interval = \DateInterval::createFromDateString('+1 day');
        $period = new \DatePeriod($begin, $interval, $end);
        Log::debug('DatePeriod', [$end, $interval, $begin]);
//        Log::debug($period);

        $i = 0;
        $items = collect([]);
        //按天查询
        foreach ($period as $date) {
            $items[$i++] = collect( $this->recordDate($date->format('Y-m-d'), $device_id) );
        }
        Log::debug($items);

        //没有记录的情况返回空
        if ( 0 == ($items->sum('payment_count')
                + $items->sum('washcar_count'))) {
            $items = collect([]);
        }
        return $items;
    }

    public function monthRecords(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $device = null;
        if (is_numeric($request->input('device_id')))
            $device = Device::local()->findOrFail($request->input('device_id'));
        $devices = Device::local()->get();
        $items = $this->records($month, $device ? $device->id : null);
//        return 'hello';
        return view('admin.center.index', compact('month', 'devices', 'device', 'items'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->monthRecords($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
