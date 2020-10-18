@extends('layouts.admin')

@section('content')

    @if (isset($item))
        <table class="table table-bordered table-sm table hover">
            <filedset>
                <legend style="text-align: center">记录详情</legend>
            </filedset>
            <tbody>
                <tr>
                    <th>{{ \App\WashCar::tr('id') }}</th>
                    <th>{{ $item->id }}</th>
                </tr>
                <tr>
                    <th>{{ \App\WashCar::tr('device_id') }}</th>
                    {{--<th>{{ $item->device_id }}</th>--}}
                    <th>
                        <a href="{{ route('device.show', $item->device_id) }}">
                            <button class="btn btn-sm btn-link">
                                {{ optional($item->device)->name }}
                            </button>
                        </a>
                    </th>
                </tr>
                <tr>
                    <th>{{ \App\WashCar::tr('trade_id') }}</th>
                    {{--<th>{{ $item->trade_id }}</th>--}}
                    <th>
                        <a href="{{ route('trade.show', $item->trade_id) }}">
                            <button class="btn btn-sm btn-link">
                                支付记录
                            </button>
                        </a>
                    </th>
                </tr>

                <tr>
                    <th>{{ \App\WashCar::tr('used_seconds') }}</th>
                    <th>{{ to_time($item->used_seconds) }}</th>
                </tr>
                <tr>
                    <th>{{ \App\WashCar::tr('total_seconds') }}</th>
                    <th>{{ to_time($item->total_seconds) }}</th>
                </tr>
                <tr>
                    <th>{{ \App\WashCar::tr('free_seconds') }}</th>
                    <th>{{ to_time($item->free_seconds) }}</th>
                </tr>

                <tr>
                    <th>{{ \App\WashCar::tr('water_seconds') }}</th>
                    <th>{{ to_time($item->water_seconds) }}</th>
                </tr>
                <tr>
                    <th>{{ \App\WashCar::tr('cleaner_seconds') }}</th>
                    <th>{{ to_time($item->cleaner_seconds) }}</th>
                </tr>
                <tr>
                    <th>{{ \App\WashCar::tr('tap_switch_seconds') }}</th>
                    <th>{{ to_time($item->tap_switch_seconds) }}</th>
                </tr>

                <tr>
                    <th>{{ \App\WashCar::tr('water_count') }}</th>
                    <th>{{ $item->water_count }}</th>
                </tr>
                <tr>
                    <th>{{ \App\WashCar::tr('cleaner_count') }}</th>
                    <th>{{ $item->cleaner_count }}</th>
                </tr>
                <tr>
                    <th>{{ \App\WashCar::tr('tap_switch_count') }}</th>
                    <th>{{ $item->tap_switch_count }}</th>
                </tr>

                <tr>
                    <th>{{ \App\WashCar::tr('temperature') }}</th>
                    <th>{{ to_float($item->temperature) }}</th>
                </tr>

                <tr>
                    <th>{{ \App\WashCar::tr('begin_at') }}</th>
                    <th>{{ $item->begin_at }}</th>
                </tr>
                <tr>
                    <th>{{ \App\WashCar::tr('end_at') }}</th>
                    <th>{{ $item->end_at }}</th>
                </tr>
                <tr>
                    <th>{{ \App\WashCar::tr('created_at') }}</th>
                    <th>{{ $item->created_at }}</th>
                </tr>
                <tr>
                    <th>{{ \App\WashCar::tr('updated_at') }}</th>
                    <th>{{ $item->updated_at }}</th>
                </tr>
                @if(isset($item->deleted_at))
                <tr>
                    <th>{{ \App\WashCar::tr('deleted_at') }}</th>
                    <th>{{ $item->deleted_at }}</th>
                </tr>
                @endif
            </tbody>
        </table>
    @else
        
    @endif
@endsection










