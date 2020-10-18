<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>测试页面</title>
</head>
<body>
    <div class="flex-center position-ref full-height">
        <div class="content">
		<p>测试页面</p>
		<a href="{{ route('payment.index', [ 'id' => 1 ]) }}">点击这里，相当于用户扫了自助洗车设备上的二维码，跳转到选择服务界面，付款后直接启动机器.</a>
        </div>
    </div>
	<p style="text-align:center;"> 鄂ICP备16000241号-2 </p>
</body>
</html>
