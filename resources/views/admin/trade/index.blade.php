@extends('layouts.admin')

@section('content')
    <div>
        <form class="form-inline" id="form" href="{{ route('trade.index') }}">

            <label for="device_id">选择设备</label>
            <select class="form-control" id="device_id" name="device_id" onchange="submitForm()">
                <option value="" {{ $device ? 'selected' : '' }}>所有设备</option>
                @foreach($devices as $curDevice)
                    <option value="{{ $curDevice->id }}" {{ $curDevice->id == optional($device)->id ? 'selected' : '' }}>{{ $curDevice->name }}</option>
                @endforeach
            </select>

            <label for="device_id">支付方式</label>
            <select class="form-control" id="type" name="type" onchange="submitForm()">
                <option value="" {{ !isset($type) ? 'selected' : '' }}>所有类型</option>
                <option value="{{ \App\Models\Trade::PaymentType_Alipay }}" {{ ($type == \App\Models\Trade::PaymentType_Alipay) ? 'selected' : '' }}>支付宝</option>
                <option value="{{ \App\Models\Trade::PaymentType_WeChat }}" {{ ($type == \App\Models\Trade::PaymentType_WeChat) ? 'selected' : '' }}>微信</option>
                <option value="{{ \App\Models\Trade::PaymentType_VipCard }}" {{ ($type == \App\Models\Trade::PaymentType_VipCard) ? 'selected' : '' }}>会员卡</option>
            </select>

            <button type="submit" class="btn btn-primary">提交</button>
        </form>
        <script>
            function submitForm()
            {
                var form = $('#form');
                form.submit();
            }
        </script>
    </div>

    @include('admin.trade.table')
@endsection

