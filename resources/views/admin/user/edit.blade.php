@extends('layouts.admin')

@section('content')

    <div class="row justify-content-md-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">基本配置</div>
                <ul>
                    <li>支付宝收款账号和真实姓名必须如实填写，否则无法收款！！！</li>
                </ul>
                <div class="card-body">
                    <form action="{{ route('user.update', $item->id) }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}

                        <div class="row form-group">
                            <label for="name" class="col-sm-2" style="text-align: right">名称:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="name" name="name" value="{{ $item->name }}" placeholder="请输入用户名">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="email" class="col-sm-2" style="text-align: right">邮箱:</label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" id="email" name="email" value="{{ $item->email }}" placeholder="请输入邮箱地址">
                            </div>
                        </div>

                        {{--<div class="row form-group">--}}
                            {{--<label for="phone" class="col-sm-2" style="text-align: right">手机:</label>--}}
                            {{--<div class="col-sm-8">--}}
                                {{--<input type="text" class="form-control" id="phone" name="phone" value="{{ $item->phone }}" placeholder="请输入手机号码">--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="row form-group">--}}
                            {{--<label for="password" class="col-sm-2" style="text-align: right">密码:</label>--}}
                            {{--<div class="col-sm-8">--}}
                                {{--<input type="text" class="form-control" id="password" name="password" value="{{ old('password') }}" placeholder="请输入密码">--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="row form-group">--}}
                            {{--<label for="role" class="col-sm-2" style="text-align: right">类型:</label>--}}
                            {{--<div class="col-sm-8">--}}
                                {{--<div class="btn-group btn-group-toggle" data-toggle="buttons">--}}
                                    {{--<label class="btn btn-sm btn-secondary">--}}
                                        {{--<input type="radio" name="role" id="role1" value="superadmin" autocomplete="off">超级管理员--}}
                                    {{--</label>--}}
                                    {{--<label class="btn btn-sm btn-secondary active">--}}
                                        {{--<input type="radio" name="role" id="role2" value="admin" autocomplete="off" checked>管理员--}}
                                    {{--</label>--}}
                                    {{--<label class="btn btn-sm btn-secondary">--}}
                                        {{--<input type="radio" name="role" id="role3" value="" autocomplete="off">普通用户--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                                {{--<input type="text" class="form-control" id="platform_fee_rate" name="platform_fee_rate" value="{{ old('platform_fee_rate') }}" placeholder="请选择用户角色">--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="row form-group">--}}
                            {{--<label for="devices" class="col-sm-2" style="text-align: right">设备:</label>--}}
                            {{--<div class="col-sm-8">--}}
                                {{--<input type="text" class="form-control" id="devices" name="devices" value="{{ old('devices') }}" placeholder="请输入设备名称，用户可见">--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="row form-group">
                            <label for="payee" class="col-sm-2" style="text-align: right">收款账号:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="payee" name="payee" value="{{ $item->payee }}" placeholder="收款的支付宝账号">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="real_name" class="col-sm-2" style="text-align: right">真实姓名:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="real_name" name="real_name" value="{{ $item->real_name }}" placeholder="收款的支付宝账号对应的真实姓名">
                            </div>
                        </div>

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
@endsection

