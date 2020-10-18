@extends('layouts.admin')

@section('content')

    <div class="row justify-content-md-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">重置密码</div>
                <div class="card-body">
                    <form action="{{ route('user.reset_password') }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('POST') }}

{{--                        <div class="row form-group">--}}
{{--                            <label for="name" class="col-sm-2" style="text-align: right">账号:</label>--}}
{{--                            <div class="col-sm-8">--}}
{{--                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="请输入当前账号">--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="row form-group">
                            <label for="password" class="col-sm-2" style="text-align: right">原密码:</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" placeholder="请输入当前使用的密码">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="password2" class="col-sm-2" style="text-align: right">新密码:</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" id="password2" name="password2" value="{{ old('password2') }}" placeholder="请输入新密码">
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

