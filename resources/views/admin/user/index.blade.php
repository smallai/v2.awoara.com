@extends('layouts.admin')

@section('content')
    @if(count($items) > 0)
    <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
        <filedset>
            <legend style="text-align: center">用户浏览</legend>
        </filedset>
        <thead>
        <tr>
            <th>{{ \App\Models\User::tr('id') }}</th>
{{--            <th>{{ \App\Models\User::tr('register_device_id') }}</th>--}}
            <th>{{ \App\Models\User::tr('name')  }}</th>
            <th>{{ \App\Models\User::tr('email') }}</th>
            <th>{{ \App\Models\User::tr('phone') }}</th>
            <th>{{ \App\Models\User::tr('payee') }}</th>
            <th>{{ \App\Models\User::tr('real_name') }}</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                {{--<td>--}}
                    {{--<a href="{{ route('device.show', $item->device_id) }}">--}}
                        {{--{{ optional($item->registerDevice)->name }}--}}
                    {{--</a>--}}
                {{--</td>--}}
                <td>{{ $item->name }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->phone }}</td>
                <td>{{ $item->payee }}</td>
                <td>{{ $item->real_name }}</td>
                <td>
                    <a href="{{ route('user.show', $item->id) }}">详情</a>
                    @role('superadmin')
                    <a onclick="event.preventDefault();document.getElementById('form-{{ $item->id }}').submit();" class="text-danger">
                        删除
                    </a>
                    <a href="{{ route('log.user.login', $item->id) }}">登录日志</a>
                    <a href="{{ route('user.force_reset_password', ['user_id' => $item->id]) }}">重置密码</a>
                    <form id="form-{{ $item->id  }}" action="{{ route('user.destroy', $item->id) }}" METHOD="POST" onclick="javascript;" style="display: inline;">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                    </form>
                    @endrole
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

