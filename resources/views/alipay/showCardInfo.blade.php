@extends('layouts.alipay')

@section('content')


    @include('alipay.deviceInfo')

    @if (isset($card))

        <div class="weui-form-preview">
            <div class="weui-form-preview__hd">
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">套餐名称</label>
                    <em class="weui-form-preview__value">{{ $card['goods_name'] }}</em>
                </div>
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">剩余次数</label>
                    <em class="weui-form-preview__value">{{ $card['total_count'] - $card['used_count'] }}</em>
                </div>
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">到期时间</label>
                    <em class="weui-form-preview__value">{{ $card['expiration'] }}</em>
                </div>
            </div>
            <br>

            <form action="{{ route('alipay.carWash') }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('POST') }}
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="device_id" value="{{ $device->id }}">
                <input type="hidden" name="card_id" value="{{ $card->id }}">
                <button type="submit" class="weui-btn weui-btn_primary" id="submit_btn">我要洗车</button>
            </form>
            <br>
        </div>
        <br>

    @endif

    @if ($errors->count())
        <div class="weui-cells">
            <div class="weui-cells__title">提示信息</div>
            @foreach ($errors->all() as $error)
                <div class="weui-cell">
                    <ol>{{ $error }}</ol>
                </div>
            @endforeach
        </div>
    @endif

@endsection

@section('script')
    <script>
        $(function () {
            $('#submit_btn').on('click', function () {
                $(this).attr('disabled',true);
                $(this).val('正在处理...');
                this.form.submit();
            })
        });
    </script>
@endsection