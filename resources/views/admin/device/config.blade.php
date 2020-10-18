@extends('layouts.admin')

@section('content')

    @role('superadmin|admin')
    @if (isset($item))
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <h3>修改设备配置</h3>
            </div>
        </div>


        <form action="{{ action('Admin\DeviceConfigController@update') }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('PUT') }}

            <input type="hidden" name="device_id" value="{{ $device_id }}">

            <br>
            <h5>第一组</h5>
            <div class="row form-group">
                <label for="light_ch1_conf" class="col-sm-2" style="text-align: right">开灯时间段1</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="light_ch1_conf" name="light_ch1_conf" value="{{ $item['light_ch1_conf'] }}" placeholder="24小时制,如： 18:00 - 22:00">
                </div>
            </div>

            <div class="row form-group">
                <label for="light_ch2_conf" class="col-sm-2" style="text-align: right">开灯时间段2</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="light_ch2_conf" name="light_ch2_conf" value="{{ $item['light_ch2_conf'] }}"  placeholder="24小时制，如： 05:00 - 08:00">
                </div>
            </div>

            <div class="row form-group">
                <label for="light_on_ch0" class="col-sm-2" style="text-align: right">空闲多久分钟自动关机:</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="light_on_ch0" name="light_on_ch0" value="{{ $item['name'] }}"  placeholder="出厂默认值为20分钟">
                </div>
            </div>

            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <button type="submit" class="btn btn-primary btn-block">提交</button>
                </div>
            </div>

        </form>
    @endif
    @else
        <h3>无此权限</h3>
        @endrole
@endsection

