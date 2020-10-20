<!doctype html>
<html lang="{{ $app->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

    {{--设置页面过期时间，后退的时候强制刷新页面--}}
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Expires" content="1">

{{--    <title>石斑鱼</title>--}}
    <link rel="stylesheet" href="{{ asset('css/weui.min.css') }}" />
    <script src="{{ asset('js/zepto.min.js') }}"></script>
</head>
<body>

<div class="page">

    {{--<div class="page__hd">--}}
    {{--<h1 class="page__title" style="margin-left: 10px">萌芽洗车</h1>--}}
    {{--</div>--}}

    <div class="page__bd">

        @section('content')
        @show

{{--            <div class="weui-msg__extra-area">--}}
{{--                <div class="weui-footer">--}}
{{--                    <p class="weui-footer__text"> 鄂ICP备16000241号-2 </p>--}}
{{--                    <p class="weui-footer__text">Copyright &copy; 2008-{{ \Carbon\Carbon::now()->format('Y') }} <a href="http://www.awoara.com">www.awoara.com</a> </p>--}}
{{--                </div>--}}
{{--            </div>--}}
    </div>

@section('script')
@show
</body>
</html>
