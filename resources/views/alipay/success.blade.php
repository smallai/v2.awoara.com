@extends('layouts.alipay')

@section('content')

    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">操作成功</h2>

            @if (($trade->payment_status === App\Models\Trade::PaymentStatus_Success)
                && ($trade->confirm_status !== App\Models\Trade::GoodsStatus_Confirmed))
                <script>
                    function refresh() {
                        location.reload(true);
                    }
                </script>
                <button class="weui-btn weui-btn_primary" onclick="refresh()">开机</button>
            @endif

            <p class="weui-msg__desc">
            <ul>
                <div class="weui-cells">
                    <div class="weui-cells__title">注意事项</div>
                    @if (($trade->payment_status === App\Models\Trade::PaymentStatus_Success)
                       && ($trade->confirm_status !== App\Models\Trade::GoodsStatus_Confirmed))
                        <div class="weui-cell">
                            <ol>网络延迟，如果已经开机，请正常使用．</ol>
                        </div>
                        <div class="weui-cell">
                            <ol>点击开机按钮，可以重试一次！</ol>
                        </div>
                        <div class="weui-cell">
                            <ol>如果不能开机，稍后自动退款！</ol>
                        </div>
                    @endif
                    <div class="weui-cell">
                        <ol>使用设备时计时，不使用不计时。</ol>
                    </div>
                    <div class="weui-cell">
                        <ol>请不要把水枪对人或动物喷射。</ol>
                    </div>
                    <div class="weui-cell">
                        <ol>感谢您的支持，祝您生活愉快！</ol>
                    </div>
                </div>
            </ul>
        </div>
    </div>
    </div>

@endsection
