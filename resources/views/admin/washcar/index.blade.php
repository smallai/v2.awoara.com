@extends('layouts.admin')

@section('content')

    @if (count($items) > 0)
        <table class="table table-bordered table-sm table-hover">
            <filedset>
                <legend style="text-align: center">洗车记录</legend>
            </filedset>
            <thead>
                <tr>
                    <th>{{ \App\WashCar::tr('id') }}</th>
                    <th>{{ \App\WashCar::tr('device_id') }}</th>
                    {{--<th>{{ \App\WashCar::tr('trade_id') }}</th>--}}
                    <th>{{ \App\WashCar::tr('used_seconds') }}</th>
                    <th>{{ \App\WashCar::tr('total_seconds') }}</th>
                    <th>{{ \App\WashCar::tr('free_seconds') }}</th>
                    <th>{{ \App\WashCar::tr('begin_at') }}</th>
                    <th>{{ \App\WashCar::tr('end_at') }}</th>
                    {{--<th>{{ \App\WashCar::tr('created_at') }}</th>--}}
                    {{--<th>{{ \App\WashCar::tr('updated_at') }}</th>--}}
                    {{--<th>{{ \App\WashCar::tr('deleted_at') }}</th>--}}
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>
                        <a href="{{ route('device.show', $item->device_id) }}">
                            <button class="btn btn-sm btn-link">
                                {{ optional($item->device)->name }}
                            </button>
                        </a>
                    </td>
                    {{--<td>--}}
                        {{--@if($item->trade_id > 0)--}}
                            {{--<a href="{{ route('trade.show', $item->trade_id) }}">--}}
                                {{--<button class="btn btn-sm btn-link">--}}
                                    {{--{{ $item->trade_id }}--}}
                                {{--</button>--}}
                            {{--</a>--}}
                        {{--@else--}}
                            {{--现金--}}
                        {{--@endif--}}
                    {{--</td>--}}
                    <td>{{ to_time($item->used_seconds) }}</td>
                    <td>{{ to_time($item->total_seconds) }}</td>
                    <td>{{ to_time($item->free_seconds) }}</td>
                    <td>{{ $item->begin_at }}</td>
                    <td>{{ $item->end_at }}</td>
                    {{--<td>{{ $item->created_at }}</td>--}}
                    {{--<td>{{ $item->updated_at }}</td>--}}
                    {{--<td>{{ $item->deleted_at }}</td>--}}
                    <td>
                        <a href="{{ route('washcar.show', $item->id) }}">
                            <button class="btn btn-sm btn-link">
                                详情
                            </button>
                        </a>

                        @if ($item->trade_id > 0)
                            <a href="{{ route('trade.show', $item->trade_id) }}">
                                <button class="btn btn-sm btn-link">
                                    支付记录
                                </button>
                            </a>

                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $items->links() }}
    @else
        <h3>无记录</h3>
    @endif

@endsection