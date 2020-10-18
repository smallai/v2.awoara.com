@extends('layouts.admin')

@section('content')
    <div>
        <form class="form-inline" id="form" href="{{ route('trade.index') }}">

            <label for="device_id">选择设备</label>
            <select class="form-control" id="device_id" name="device_id" onchange="submitForm()">
                <option value="" {{ $device ? 'selected' : '' }}>所有设备</option>
                @foreach($devices as $curDevice)
                    <option value="{{ $curDevice->id }}" {{ $curDevice->id == optional($device)->id ? 'selected' : '' }}>{{ $curDevice->name }}</option>
                @endforeach
            </select>
            {{--<button type="submit" class="btn btn-primary">提交</button>--}}
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
                <legend style="text-align: center">套餐浏览</legend>
            </filedset>
        <thead>
        <tr>
            <th>{{ \App\Models\Goods::tr('id') }}</th>
            {{--<th>设备名称</th>--}}
            <th>{{ \App\Models\Goods::tr('device_id') }}</th>
            <th>{{ \App\Models\Goods::tr('owner_id') }}</th>
            <th>{{ \App\Models\Goods::tr('name')  }}</th>
            <th>{{ \App\Models\Goods::tr('price') }}</th>
            {{--<th>{{ \App\Models\Goods::tr('image') }}</th>--}}
            <th>{{ \App\Models\Goods::tr('is_sale') }}</th>
            <th>{{ \App\Models\Goods::tr('is_recommend') }}</th>
            <th>{{ \App\Models\Goods::tr('seconds') }}</th>
{{--            @role('superadmin')--}}
            <th>{{ \App\Models\Goods::tr('count') }}</th>
            <th>{{ \App\Models\Goods::tr('today_limit') }}</th>
            <th>{{ \App\Models\Goods::tr('days') }}</th>
{{--            @endrole--}}
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>
                    <a href="{{ route('goods.show', $item->id) }}">{{ $item->id }}</a>
                </td>
                <td>
                    <a href="{{ route('device.show', $item->device_id) }}">{{ optional($item->device)->name }}</a>
                </td>
                <td>
                    <a href="{{ route('user.show', $item->owner_id ?? 0) }}">{{ optional($item->owner)->name }}</a>
                </td>
                <td>{{ $item->name }}</td>
                <td>{{ to_float($item->price) }}</td>
                {{--<td><img src = "{{ $item->image }}" class="img-thumbnail" width="64px" height="64px"></td>--}}
                <td>{{ \App\Models\Goods::tr('is_sale', $item->is_sale) }}</td>
                <td>{{ \App\Models\Goods::tr('is_recommend', $item->is_recommend) }}</td>
                <td>{{ to_time($item->seconds) }}</td>
{{--                @role('superadmin')--}}
                <td>{{ $item->count }}</td>
                <td>{{ $item->today_limit }}</td>
                    <td>{{ $item->days }}</td>
{{--                @endrole--}}
                <td>
                    {{--<a href="{{ route('goods.show', $item->id) }}">详情</a>--}}
                    {{--@role('super_admin')--}}
                    {{--@role('superadmin')--}}
                    <a href="{{ route('goods.edit', $item->id) }}">编辑</a>
                    {{--<a href="{{ route('goods.edit', $item->id) }}">删除</a>--}}
                    <a onclick="event.preventDefault();document.getElementById('form-{{ $item->id }}').submit();" class="text-danger">
                        删除
                    </a>
                    {{--@endrole--}}
                {{--@endrole--}}

                <form id="form-{{ $item->id  }}" action="{{ route('goods.destroy', $item->id) }}" METHOD="POST" onclick="javascript;" style="display: inline;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                </form>

            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $items->links() }}

    @else
        <h3>无记录</h3>
    @endif
@endsection

