{{--<!DOCTYPE html>--}}
{{--<html lang="{{ app()->getLocale() }}">--}}
{{--<head>--}}
    {{--<meta charset="utf-8">--}}
    {{--<meta http-equiv="X-UA-Compatible" content="IE=edge">--}}
    {{--<meta name="viewport" content="width=device-width, initial-scale=1">--}}
    {{--<meta name="csrf-token" content="{{ csrf_token() }}">--}}
    {{--<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">--}}
{{--</head>--}}
{{--<body>--}}
{{--<div>--}}
        {{--<a href="{{ $url }}">点击跳转到支付宝</a>--}}
{{--</div>--}}
{{--</body>--}}
{{--</html>--}}

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
</head>
<body>
<div>
    <script src="https://gw.alipayobjects.com/as/g/h5-lib/alipayjsapi/3.1.1/alipayjsapi.js"></script>
    <style>
        .output{ display:block; max-width: 100%; overflow: auto}
    </style>
    <button id="J_btn_location" class="btn btn-default">执行</button>
    <pre id="J_output" class="output"></pre>
    <script>
        var btnLocation = document.querySelector('#J_btn_location');
        var output = document.querySelector('#J_output');
        btnLocation.addEventListener('click', function(){
            ap.getAuthCode(function(res) {
                output.innerHTML = JSON.stringify(res, undefined, '  ');
            });
        });
    </script>
</div>
</body>
</html>



