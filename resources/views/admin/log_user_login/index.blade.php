@extends('layouts.admin')

@section('content')
    @if (count($logs) > 0)
        <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
            <filedset>
                <legend style="text-align: center">登录日志</legend>
            </filedset>

            <tbody>
            <th>{{ \App\Models\LogUserLogin::tr('id') }}</th>
            <th>{{ \App\Models\LogUserLogin::tr('ip') }}</th>
            <th>{{ \App\Models\LogUserLogin::tr('src') }}</th>
            <th>{{ \App\Models\LogUserLogin::tr('created_at') }}</th>
            @role('superadmin')
                <th>{{ \App\Models\LogUserLogin::tr('device') }}</th>
                <th>{{ \App\Models\LogUserLogin::tr('platform') }}</th>
                <th>{{ \App\Models\LogUserLogin::tr('browser') }}</th>
                <th>{{ \App\Models\LogUserLogin::tr('platform_version') }}</th>
                <th>{{ \App\Models\LogUserLogin::tr('browser_version') }}</th>
                <th>{{ \App\Models\LogUserLogin::tr('remark') }}</th>
            @endrole

            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>
                        <a href="{{ 'https://www.baidu.com/s?wd='.$log->ip }}">{{ $log->ip }}</a>
                    </td>
                    <td>{{ $log->src }}</td>
                    <td>{{ $log->created_at }}</td>
                    @role('superadmin')
                        <td>{{ $log->device }}</td>
                        <td>{{ $log->platform }}</td>
                        <td>{{ $log->browser }}</td>
                        <td>{{ $log->platform_version }}</td>
                        <td>{{ $log->browser_version }}</td>
                        <td>{{ $log->remark }}</td>
                    @endrole
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $logs->links() }}
    @else
        <h3>无记录</h3>
    @endif
@endsection
