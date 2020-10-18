<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <link rel="stylesheet" href="{{ asset('css/weui.min.css') }}" />
    <title></title>
</head>
<body>
<div class="page">
    <div class="weui-msg">
        @if ($paymentSuccess)
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
                    <button class="weui-btn_default" onclick="refresh()">刷新页面</button>
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
                                <ol>点击刷新页面，可以重试一次！</ol>
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
        @elseif (false)
            <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
            <div class="weui-msg__text-area">
                <h2 class="weui-msg__title">操作失败</h2>
                <p class="weui-msg__desc">网络延迟,如果已经开机,请正常使用，如果不能开机，稍后自动退款！</p>
            </div>
            @elseif (false)
            <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
            <div class="weui-msg__text-area">
                <h2 class="weui-msg__title">操作失败</h2>
                <p class="weui-msg__desc">付款失败，请稍后重试。给您带来的不便，敬请谅解！</p>
            </div>
        @endif
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__text"> 鄂ICP备16000241号-2 </p>
                <p class="weui-footer__text">Copyright &copy; 2008-{{ \Carbon\Carbon::now()->format('Y') }} www.awoara.com </p>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/zepto.min.js') }}"></script>
</body>
</html>
