@extends('layouts.admin')

@section('content')

    <style>
        body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #eee;
        }

        .form-signin {
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
        }
        .form-signin .form-signin-heading,
        .form-signin .checkbox {
            margin-bottom: 10px;
        }
        .form-signin .checkbox {
            font-weight: normal;
        }
        .form-signin .form-control {
            position: relative;
            height: auto;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            padding: 10px;
            font-size: 16px;
        }
        .form-signin .form-control:focus {
            z-index: 2;
        }
        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }
        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>

    <div class="container">

        {{-- 显示错误信息 --}}
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 显示通知信息 --}}
        <div class="col-sm-12">
            @include('flash::message')
        </div>

        <div class="col-4 col-offset-4">
            <form class="form-signin" method="POST" action="{{ route('admin.login') }}">
                <h2 class="form-signin-heading">管理员登录</h2>
                {{ csrf_field() }}
                <label for="inputEmail" class="sr-only">Email address</label>
                <input type="text" id="inputEmail" class="form-control" placeholder="请输入邮箱或手机号码"
                       name="login" value="{{ old('login') }}"　required autofocus>
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" id="inputPassword" class="form-control" name="password" placeholder="请输入密码" required>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="1" name="remember"> 记着密码
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">登录</button>
            </form>
        </div>

        @if (0)
        <div class="row justify-content-md-center mt-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">登录</div>
                    <div class="card-body">
                        <form class="form-horizontal" method="POST" action="{{ route('admin.login') }}">
                            {{ csrf_field() }}

                            <div class="form-group row">
                                <label for="login" class="col-lg-4 col-form-label text-lg-right">账号</label>

                                <div class="col-lg-6">
                                    <input
                                            id="login"
                                            type="text"
                                            class="form-control{{ $errors->has('login') ? ' is-invalid' : '' }}"
                                            name="login"
                                            value="{{ old('login') }}"
                                            placeholder="请输入邮箱或手机号码"
                                            required
                                            autofocus
                                    >

                                    @if ($errors->has('login'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-lg-4 col-form-label text-lg-right">密码</label>

                                <div class="col-lg-6">
                                    <input
                                            id="password"
                                            type="password"
                                            class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                            name="password"
                                            placeholder="请输入密码"
                                            required
                                    >

                                    @if ($errors->has('password'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-6 offset-lg-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1" {{ 1 == old('remember') ? 'checked' : '' }}> 记着密码
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-6 offset-lg-4">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        登录
                                    </button>

                                    {{--<a class="btn btn-link" href="{{ route('password.request') }}">--}}
                                    {{--Forgot Your Password?--}}
                                    {{--</a>--}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @endif
    </div>
@endsection
