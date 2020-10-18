<!DOCTYPE html>
<html>
<head>
    <title>石斑鱼自助洗车管理系统</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />

    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    {{--<script src="https://cdn.bootcss.com/jquery/2.1.1/jquery.min.js"></script>--}}
    {{--<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>--}}

    {{--<link rel="stylesheet" href="{{ mix('css/app.css') }}" />--}}
     {{--<link rel="stylesheet" type="text/css" href="{{ mix('css/font-awesome.min.css') }}"> --}}

    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>

    @section('style')
    @endsection

    <style>
    </style>
</head>
<body>

@if (Auth::guest())

@section('content')
@show

@else

@include('layouts.nav')

<div class="container-fluid">
    {{--<div style="margin-top: 0.1%;"></div>--}}

    <div class="col-sm-12">
        @include('flash::message')
    </div>

    <div style="margin-bottom: 2%;"></div>

    @if($errors->count())
        <ul>
            @foreach ($errors->all() as $error)
                <li class="text-warning">{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @section('content')
    @show
</div>

@endif

<div id="app"></div>

    @section('script')
    @endsection

{{--<script src="{{ mix('js/app.js') }}"></script>--}}
 {{--<script type="text/javascript" src="{{ assert('js/moment.min.js') }}"></script>--}}
 {{--<script type="text/javascript" src="{{ assert('js/daterangepicker.js') }}"></script>--}}

<script type="text/javascript">
    $('#flash-overlay-modal').modal();
    $(document).ready(function() {
        $('div.alert').not('.alert-important').delay(3000).fadeOut(500);
    });
</script>

@stack('scripts')

</body>
</html>
