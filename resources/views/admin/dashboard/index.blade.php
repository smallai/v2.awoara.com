@extends('layouts.admin')

@section('content')
    @if (count($devices) > 0)
            <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
                <tr>
                    <th>网点</th>
                    <th>网络</th>
                    <th>今日洗车</th>
                    <th>昨日洗车</th>
                    <th>可提现</th>
                    <th>操作</th>
                </tr>
                    @foreach($devices  as $device)
                        @if (!$device['is_online'])
                            <tr class="table-danger">
                        @else
                            <tr class="table-success">
                        @endif

                        {{--网点--}}
                        <td>
                            <a href="{{ route('device.show', ['id' => $device['id']]) }}">
                                {{ $device['name'] }}
                            </a>
                        </td>

                        {{--在线状态--}}
                        <td>
                            @if ($device['is_online'])
                                <span class="text-success">在线</span>
                            @else
                                <span class="text-danger">离线</span>
                            @endif
                        </td>

                        {{--今日洗车数量--}}
                        <td>
                            @if ($device['today_washcar_count'] )
                                <span class="text-success">{{ $device['today_washcar_count'] }}</span>
                            @else
                                <span class="text-warning">{{ $device['today_washcar_count'] }}</span>
                            @endif
                        </td>

                        {{--　昨日洗车数量　--}}
                        <td>
                            @if ($device['yesterday_washcar_count'] )
                                <span class="text-success">{{ $device['yesterday_washcar_count'] }}</span>
                            @else
                                <span class="text-warning">{{ $device['yesterday_washcar_count'] }}</span>
                            @endif
                        </td>

                        {{--可提现金额--}}
                        <td>
                            <span class="text-success">{{ to_float($device['withdraw_money']) }}</span>
                        </td>

                        <td>
                            @if (\Illuminate\Support\Facades\Auth::id() === $device['owner_id'])
                                <a href="{{ route('withdraw.store') }}" onclick="event.preventDefault();document.getElementById('form-{{ $device->id }}').submit();">
                                    提现
                                </a>
                            @endif
                                @role('superadmin')
                                <a href="{{ route('withdraw.store') }}" onclick="event.preventDefault();document.getElementById('form-{{ $device->id }}').submit();">
                                    提现
                                </a>
                                @endrole

                                <a href="{{ route('device.qrcode', ['device_id' => $device['id']]) }}">二维码</a>
                        </td>
                    </tr>

                        @if ($device->withdraw_money > 0)
                            <form id="form-{{ $device->id  }}" action="{{ route('withdraw.store') }}" METHOD="POST" onclick="javascript;" style="display: inline;">
                                {{ csrf_field() }}
                                {{ method_field('POST') }}
                                <input type="hidden" name="snap_uuid" value="{{ $device->snap_uuid }}">
                                <input type="hidden" name="device_id" value="{{ $device->id }}">
                                <input type="hidden" name="owner_id" value="{{ $device->owner_id }}">
                                <input type="hidden" name="withdraw_money" value="{{ $device->withdraw_money }}">
                                <button type="submit" class="btn btn-sm btn-link"></button>
                            </form>
                        @endif
                    @endforeach
            </table>
            {{ $devices->links() }}
        @endif

@endsection
