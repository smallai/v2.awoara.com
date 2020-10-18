@extends('layouts.wap')

@section('content')

    <form id="form" action="{{ route('wap.login') }}" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="weui-cell">
        <div class="weui-cell__hd">
            <label class="weui-label">手机号</label>
        </div>
        <div class="weui-cell__bd">
            <input class="weui-input" type="tel" placeholder="请输入手机号" id="mobile" name="phone" value="{{ old('phone') }}">
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd">
            <label class="weui-label">验证码</label>
        </div>
        <div class="weui-cell__bd">
            <input class="weui-input" type="tel" placeholder="请输入验证码" id="code" name="code" value="{{ old('code') }}">
        </div>
        <div class="weui-cell__ft">
            <button type="button" class="weui-btn weui-btn_mini weui-btn_primary" id="getCode">获取</button>
            <button type="button" class="weui-btn weui-btn_mini weui-btn_warn" id="timeCode">60秒</button>
        </div>
    </div>

    <div class="weui-cell">
        {{--{{ method_field('POST') }}--}}
        <button class="weui-btn weui-btn_primary">注册并登录</button>
    </div>
    </form>

    @if($errors->count())
        <ul>
            @foreach ($errors->all() as $error)
                <li class="text-warning">{{ $error }}</li>
            @endforeach
        </ul>
    @endif

@endsection

@section('script')
    <script>
        $("#timeCode").hide();
        $("#getCode").click(()=>{
            var mobile=$("#mobile").val();
            var reg = /^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\d{8}$/;
            if(reg.test(mobile)){
                sendCode();
                reserveCode();
            }else {
                alert("手机号格式错误")
            }
        });

        function reserveCode(){
            var time=0;
            $("#timeCode").show();
            $("#getCode").hide();
            timer=setInterval(function(){
                time=parseInt($("#timeCode").html());
                time--;
                $("#timeCode").html(time+"秒");
                if (time==0) {
                    $("#timeCode").html("60秒").hide();
                    $("#getCode").show();
                    clearInterval(timer);
                }
            },1000)
        }
        
        function sendCode() {
            console.log('send code');
            $.ajax({
                type: 'POST',
                url: '{{ route('send_verification_code') }}',
                data: {
                    phone: $('#mobile').val()
                },
                dataType: 'json',
                success: function (data, status, xhr) {
                    console.log(data);
                },
                error: function (xhr, errorType, error) {
                    console.log(errorType);
                },
            })
        }

        // sendCode();

    </script>
@endsection