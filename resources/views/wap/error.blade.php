<!doctype html>
<html lang="{{ $app->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>萌芽洗车</title>
    <link rel="stylesheet" href="{{ asset('css/weui.min.css') }}" />
    <script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
</head>
<body>

<div class="page">

    {{--<div class="page__hd">--}}
    {{--<h1 class="page__title" style="margin-left: 10px">萌芽洗车</h1>--}}
    {{--</div>--}}

    <div class="page__bd">

        <div class="weui-msg">
            <div class="weui-msg__icon-area"><i class="weui-icon-info weui-icon_msg"></i></div>
            <div class="weui-msg__text-area">
                <h2 class="weui-msg__title">操作提示</h2>
                <p class="weui-msg__desc">
                <ul>
                    <div class="weui-cells">
                        @if ($errors->count())
                            <div class="weui-cells__title">提示信息</div>
                            @foreach ($errors->all() as $error)
                                <div class="weui-cell">
                                    <ol>{{ $error }}</ol>
                                </div>
                            @endforeach
                        @else

                        @endif
                    </div>
                </ul>
            </div>
        </div>

        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__text"> 鄂ICP备16000241号-2 </p>
                <p class="weui-footer__text">Copyright &copy; 2008-{{ \Carbon\Carbon::now()->format('Y') }} <a href="www.awoara.com">www.awoara.com</a> </p>
            </div>
        </div>
    </div>

@section('script')
@show
</body>
</html>


