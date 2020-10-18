@extends('layouts.alipay')

@section('content')

    @include('alipay.deviceInfo')

    <div class="weui-msg__text-area">
        <h2 class="weui-msg__title"></h2>
        <div class="weui-panel">
            <form action="{{ route('alipay.order') }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('POST') }}
                <input type="hidden" name="device_id" id="device_id" value="{{ $device->id }}">
                <div class="weui-cells__title">
					套餐选项
                </div>
                <div class="weui-cells weui-cells_radio">
                    @foreach($items as $item)
                        <label class="weui-cell weui-check__label" for="{{ 'x'.$item->id }}">
                            <div class="weui-cell__bd">
                                <p>{{ $item->name }}</p>
                            </div>
                            <div class="weui-cell__ft">
                                <input type="radio" class="weui-check goods" name="goods"
                                       value="{{ $item->id.','.to_float($item->price) }}"
                                       data-desc="描述信息1" id="{{ 'x'.$item->id }}"
                                {{ $item->is_recommend ? 'checked='.'"true"' : '' }}"/>
                                <span class="weui-icon-checked"></span>
                            </div>
                        </label>
                    @endforeach
                </div>
                <div class="weui-form-preview">
                    <div class="weui-form-preview__hd">
                        <div class="weui-form-preview__item">
                            <label class="weui-form-preview__label">付款金额</label>
                            <em class="weui-form-preview__value">¥<span id="total_money">0.00</span></em>
                        </div>
                    </div>
                </div>
                <div>
                    <button type="submit" class="weui-btn weui-btn_primary">确定</button>
                </div>
            </form>
        </div>
        <p class="weui-msg__desc">
        <ul>
            <div class="weui-cells">
                <div class="weui-cells__title">温馨提示</div>

                @if (isset($card))
                    @if($today_count >= $card->today_limit)
                        <div class="weui-cell weui-cell_primary">
                            <ol>您的会员卡已超出当日可用次数(今日已用{{ $today_count }}次)!</ol>
                        </div>
                    @endif

                    @if (UserHaveNotMatchVipCard($device, $card))
                        <div class="weui-cell weui-cell_warn">
                            <ol>您的会员卡不适用于该设备,请付费使用！(会员卡不通用！)</ol>
                        </div>
                    @elseif (UserHaveExpiredVipCard($device, $card))
                        <div class="weui-cell weui-cell_warn">
                            <ol>您的会员卡已过期,过期时间: {{$card['expiration']}}!</ol>
                        </div>
                    @endif
                @endif

                <div class="weui-cell">
                    <ol>使用设备开始计时，不使用不计时。</ol>
                </div>
                <div class="weui-cell">
                    <ol>请不要把水枪对人或动物喷射。</ol>
                </div>
            </div>
        </ul>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/zepto.min.js') }}"></script>
    <script>
        function update(obj)
        {
            if ($(obj).attr('checked'))
            {
                $data = $(obj).val().split(',');
                $desc = $(obj).data('desc');
                $('#total_money').html($data[1]);
                $('#desc').html($desc);
                console.log($data[1]);
                console.log($desc);
            }
        }

        $('.goods').change(function() {
            console.log('toggle');
            console.log(this);
            $(this).attr('checked', true);
            update(this);
        });

        function init() {
            $items = $('.goods');
            for($i=0; $i<$items.length; $i++) {
                update($items[$i]);
            }
        };

        init();

    </script>
@endsection

