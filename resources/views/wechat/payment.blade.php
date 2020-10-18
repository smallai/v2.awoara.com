@extends('layouts.wechat')

@section('content')
    <div>
        <script>
            function redirect() {
                window.location.href = "{{ route('wechat.returnUrl', ['trade_id' => $trade->id]) }}";
            }
        </script>
        <h4>如果没有自动跳转，请点击我要洗车按钮。</h4>
        <button class="weui-btn_default" onclick="redirect()">我要洗车</button>
    </div>

@endsection

@section('script')
    <script>
        wx.config({!! $wx_config !!});

        wx.ready(function(){
            // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，
            // config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，
            // 则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，
            // 则可以直接调用，不需要放在ready函数中。
            console.log('ready');
        });

        wx.error(function(res){
            // config信息验证失败会执行error函数，如签名过期导致验证失败，
            // 具体错误信息可以打开config的debug模式查看，
            // 也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
            console.log(res);
        });

        function callPay1() {
            wx.chooseWXPay({
                timestamp: '{!! $pay_config['timestamp'] !!}', // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                nonceStr: '{!! $pay_config['nonceStr'] !!}', // 支付签名随机串，不长于 32 位
                package: '{!! $pay_config['package'] !!}', // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
                signType: '{!! $pay_config['signType'] !!}', // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                paySign: '{!! $pay_config['paySign'] !!}', // 支付签名
                success: function (res) {
                    // 支付成功后的回调函数
                    console.log(res);
                },
                // fail: function(res) {
                //     console.log(res);
                // },
                complate: function (res) {
                    console.log(res);
                }
            });
        }

        function onBridgeReady() {
            console.log('onBridgeReady');
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                    {!! $bridge_config !!},
                function(res){

                    console.log(res);
                    if(res.err_msg == "get_brand_wcpay_request:ok" ){
                        // 使用以上方式判断前端返回,微信团队郑重提示：
                        //res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                        window.location.href = "{{ route('wechat.returnUrl', ['trade_id' => $trade->id]) }}";
                    }
                    else
                    {
                        window.location.href = "{{ route('wechat.returnUrl', ['trade_id' => $trade->id]) }}";
                    }
                });
        }

        function callPay2()
        {
            console.log('callPay2');
            if (typeof WeixinJSBridge == "undefined") {
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                    document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
                }
            }else{
                onBridgeReady();
            }
        }

        callPay2();

    </script>
@endsection