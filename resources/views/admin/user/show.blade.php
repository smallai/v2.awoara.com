@extends('layouts.admin')

@section('content')
    @if(isset($item))
        <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
        {{--<fieldset>--}}
            <legend style="text-align: center">详情</legend>
        {{--</fieldset>--}}
        <tbody>
        <tr>
            <th>{{ \App\Models\User::tr('id') }}</th>
            <td>{{ $item->id }}</td>
        </tr>
        <tr>
            <th>{{ \App\Models\User::tr('name') }}</th>
            <td>{{ $item->name }}</td>
        </tr>
        <tr>
            <th>{{ \App\Models\User::tr('email') }}</th>
            <td>{{ $item->email }}</td>
        </tr>
        <tr>
            <th>{{ \App\Models\User::tr('phone') }}</th>
            <td>{{ $item->phone }}</td>
        </tr>
        <tr>
            <th>{{ \App\Models\User::tr('payee') }}</th>
            <td>{{ $item->payee }}</td>
        </tr>
        <tr>
            <th>{{ \App\Models\User::tr('real_name') }}</th>
            <td>{{ $item->real_name }}</td>
        </tr>
        {{--<tr>--}}
            {{--<th>{{ \App\Models\User::tr('page_size') }}</th>--}}
            {{--<td>{{ $item->page_size }}</td>--}}
        {{--</tr>--}}
        <tr>
            <th>{{ \App\Models\User::tr('created_at') }}</th>
            <td>{{ $item->created_at }}</td>
        </tr>
        <tr>
            <th>{{ \App\Models\User::tr('updated_at') }}</th>
            <td>{{ $item->updated_at }}</td>
        </tr>
        @if (isset($item->deleted_at))
            <tr>
                <th>{{ \App\Models\User::tr('deleted_at') }}</th>
                <td>{{ $item->deleted_at }}</td>
            </tr>
        @endif
        </tbody>
    </table>
    @else
    <h3>无记录</h3>
    @endif
@endsection

