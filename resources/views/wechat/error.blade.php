@extends('layouts.wechat')

@section('content')

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

@endsection

