<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <link rel="stylesheet" href="{{ asset('css/weui.min.css') }}" />
</head>
<body>
<div>
    @section('content')
    @show

        <script src="{{ asset('js/zepto.min.js') }}"></script>
        <script src="{{ asset('js/zepto.csrf.min.js') }}"></script>
    @section('script')
    @show
</div>
</body>
</html>
