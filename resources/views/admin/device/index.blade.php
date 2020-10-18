@extends('layouts.admin')

@section('content')
    @if(count($items) > 0)
        <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
            <filedset>
                <legend style="text-align: center">设备浏览</legend>
            </filedset>
        <thead>
        <tr>
            <th>{{ \App\Models\Device::tr('id') }}</th>
            <th>{{ \App\Models\Device::tr('name')  }}</th>
            <th>{{ \App\Models\Device::tr('is_online') }}</th>
            @role('superadmin')
                <th>{{ \App\Models\Device::tr('owner_id') }}</th>
                <th>{{ \App\Models\Device::tr('phone') }}</th>
                <th>{{ \App\Models\Device::tr('platform_fee_rate') }}</th>
                <th>{{ \App\Models\Device::tr('vip_card_today_limit') }}</th>
            @endrole
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            @if ($item['is_online'])
                <tr class="">
            @else
                <tr class="table-danger">
            @endif
                {{--<td>{{ $item->id }}</td>--}}
                <td>
                    <a href="{{ route('device.show', $item->id) }}">{{ $item->id }}</a>
                </td>
                <td><a href="{{ route('device.show', $item->id) }}">{{ $item->name }}</a></td>
                @if ($item['is_online'])
                    <td class="text-success">在线</td>
                @else
                    <td class="text-danger">离线</td>
                @endif
                @role('superadmin')
                    <td><a href="{{ route('user.show', $item->owner_id ?? 0) }}">{{ optional($item->owner)->real_name }}</a></td>
                    <td>{{ $item->phone }}</td>
                    <td>{{ to_float($item->platform_fee_rate, 3) }}</td>
                    <td>{{ $item->vip_card_today_limit }}</td>
                @endrole
                <td>
                    {{--<a href="{{ route('device.show', $item->id) }}">详情</a>--}}
                    <a href="{{ route('device.qrcode', ['device_id' => $item->id]) }}" class="text-info">二维码</a>
                    <a href="{{ route('device.edit', $item->id) }}" class="text-info">编辑</a>
                    @role('superadmin')
                    <a onclick="event.preventDefault();document.getElementById('form-{{ $item->id }}').submit();" class="text-danger">
                        删除
                    </a>
                    <a href="{{ route('device.show_set_admin', ['device_id' => $item->id]) }}" class="text-info">设置管理员</a>
                    @endrole
                </td>

                @role('superadmin')
                <form id="form-{{ $item->id  }}" action="{{ route('device.destroy', $item->id) }}" METHOD="POST" onclick="javascript;" style="display: inline;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                </form>
                @endrole

            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $items->links() }}

    @else
        <h3>无记录</h3>
    @endif
@endsection

