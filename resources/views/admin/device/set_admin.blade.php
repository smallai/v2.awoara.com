@extends('layouts.admin')

@section('content')
    @if(isset($device) && count($users) > 0)
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        指定设备管理员:
                        (<span>
                            设备编号: {{ $device['id'] }}
                        </span>
                        <span>
                            网点名称: {{ $device['name'] }}
                        </span>)
                    </div>
                    <div class="card-body">
                        <form role="form" action="{{ route('device.set_admin') }}" method="POST">
                            {{ csrf_field() }}
                            {{ method_field('POST') }}

                            <input type="hidden" name="device_id" value="{{ $device['id'] }}">

                            <div class="row form-group">
                                <label for="owner_id" class="col-sm-2 text-right">管理员:</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="owner_id" name="owner_id">
                                        @foreach($users as $user)
                                            <option value="{{ $user['id'] }}" {{ $device['owner_id'] === $user['id'] ? 'selected="selected"' : '' }}>
                                                <span>{{ $user['real_name'] }}</span>
                                                (<span>{{ $user['phone'] }}</span>)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-8">
                                    <button type="submit" class="btn btn-primary btn-block">提交</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <h3>无权限</h3>
    @endif
@endsection

