@extends('layouts.admin')

@section('content')

    @role('superadmin')
    <div class="row justify-content-md-center">
        <div class="col-md-10">
            <div class="card">
                {{--<div class="card-header">创建设备</div>--}}
                <div class="card-body">
                    <form action="{{ route('device.store') }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('POST') }}

                        <div class="row form-group">
                            <label for="product_key" class="col-sm-2" style="text-align: right">产品标识:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="product_key" name="product_key" value="{{ old('product_key') }}" placeholder="请输入产品标识,必须正确填写">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="device_name" class="col-sm-2" style="text-align: right">设备标识:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="device_name" name="device_name" value="{{ old('device_name') }}" placeholder="请输入设备标识，必须正确填写">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="device_secret" class="col-sm-2" style="text-align: right">设备密钥:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="device_secret" name="device_secret" value="{{ old('device_secret') }}" placeholder="请输入设备秘钥，必须正确填写">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="platform_fee_rate" class="col-sm-2" style="text-align: right">平台费率(‰):</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="platform_fee_rate" name="platform_fee_rate" value="{{ old('platform_fee_rate') }}" placeholder="请输入平台费率，如千分之1，则输入1">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="name" class="col-sm-2" style="text-align: right">设备名称:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="请输入设备名称，用户可见">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="address" class="col-sm-2" style="text-align: right">设备地址:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" placeholder="请输入设备地址，省市区街道，填写正确，用于定位，引导用户去洗车。">
                            </div>
                        </div>

{{--                        <div class="row form-group">--}}
{{--                            <label for="address" class="col-sm-2" style="text-align: right">会员卡当天最多使用次数:</label>--}}
{{--                            <div class="col-sm-8">--}}
{{--                                <input type="text" class="form-control" id="address" name="vip_card_today_limit" value="{{ old('vip_card_today_limit') ?? 1 }}" placeholder="请输入会员卡当天最多使用次数。">--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="row">
                            <div class="col-sm-2">
                            </div>
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
        <h3>无此权限</h3>
    @endrole
@endsection

