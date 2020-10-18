@extends('layouts.admin')

@section('content')

    <div>
        <form class="form-inline" id="form" href="{{ route('card.index') }}">

            <label for="device_id">选择设备</label>
            <select class="form-control" id="device_id" name="device_id" onchange="submitForm()">
                <option value="" {{ $device ? 'selected' : '' }}>所有设备</option>
                @foreach($devices as $curDevice)
                    <option value="{{ $curDevice->id }}" {{ $curDevice->id == optional($device)->id ? 'selected' : '' }}>{{ $curDevice->name }}</option>
                @endforeach
            </select>
        </form>
        <script>
            function submitForm()
            {
                form = $('#form');
                form.submit();
            }
        </script>
    </div>

    @if(count($items) > 0)
        <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
            <filedset>
                <legend style="text-align: center">会员卡浏览</legend>
            </filedset>
            <thead>
            <tr>
{{--                <th>{{ \App\Models\UserVipCard::tr('id')  }}</th>--}}
                <th>{{ \App\Models\UserVipCard::tr('device_id')  }}</th>
                <th>{{ \App\Models\UserVipCard::tr('trade_id') }}</th>
{{--                <th>{{ \App\Models\UserVipCard::tr('user_openid') }}</th>--}}
                <th>{{ \App\Models\UserVipCard::tr('goods_name') }}</th>
                <th>{{ \App\Models\UserVipCard::tr('seconds') }}</th>
                <th>{{ \App\Models\UserVipCard::tr('used_count') }}</th>
                <th>{{ \App\Models\UserVipCard::tr('total_count') }}</th>
                <th>{{ \App\Models\UserVipCard::tr('today_limit') }}</th>
                <th>{{ \App\Models\UserVipCard::tr('expiration') }}</th>
                <th>{{ \App\Models\UserVipCard::tr('created_at') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
{{--                    <td>{{ $item->id }}</td>--}}
                    <td><a href="{{ route('device.show', optional($item->device)->id) }}">{{ optional($item->device)->name }}</a></td>
                    <td><a href="{{ route('trade.show', $item->trade_id) }}">{{ $item->trade_id }}</a></td>
{{--                    <td>{{ $item->user_openid }}</td>--}}
                    <td>{{ $item->goods_name }}</td>
                    <td>{{ to_time($item->seconds) }}</td>
                    <td>{{ $item->used_count }}</td>
                    <td>{{ $item->total_count }}</td>
                    <td>{{ $item->today_limit }}</td>
                    <td>{{ $item->expiration }}</td>
                    <td>{{ $item->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $items->links() }}
    @else
        <h3>无记录</h3>
    @endif
@endsection

