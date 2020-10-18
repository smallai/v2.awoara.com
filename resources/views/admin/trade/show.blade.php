@extends('layouts.admin')

@section('content')
@if(isset($item))
    <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
        <filedset>
            <legend style="text-align: center">交易详情</legend>
        </filedset>
        <tbody>
        <tr>
            <th>{{ \App\Models\Trade::tr('id') }}</th>
            <td>{{ $item->id }}</td>
        </tr>

        @if ($item->user)
        <tr>
            <th>用户名称</th>
            <td>
                <a href="{{ route('user.show', optional($item->user)->id) }}">{{ optional($item->user)->name }}</a>
            </td>
        </tr>
        @endif

        <tr>
            <th>{{ \App\Models\Trade::tr('user_openid') }}</th>
            <td>{{ $item->user_openid }}</td>
        </tr>

        <tr>
            <th>设备名称</th>
            <td>
                <a href="{{ route('device.show', optional($item->device)->id) }}">{{ optional($item->device)->name }}</a>
            </td>
        </tr>

        <tr>
            <th>设备管理员</th>
            <td>
                <a href="{{ route('user.show', optional($item->owner)->id) }}">{{ optional($item->owner)->real_name }}</a>
            </td>
        </tr>

        <tr>
            <th>商品</th>
            <td>
                {{ $item->goods_name  }}
                <a href="{{ route('goods.show', $item->goods_id) }}">{{ $item->goods_name }}</a>
            </td>
        </tr>

        @if ($item->washcar_id > 0)
        <tr>
            <th>{{ \App\Models\Trade::tr('washcar_id') }}</th>
            <td>{{ $item->washcar_id }}</td>
            <th>洗车记录</th>
            <td>
                <a href="{{ route('washcar.show', $item->washcar_id) }}">{{ $item->washcar_id }}</a>
            </td>
        </tr>
        @endif

        <tr>
            <th>{{ \App\Models\Trade::tr('confirm_status') }}</th>
            <td>{{ \App\Models\Trade::tr('confirm_status', $item->confirm_status) }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('confirmed_at') }}</th>
            <td>{{ $item->confirmed_at }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('payment_status') }}</th>
            <td>{{ \App\Models\Trade::tr('payment_status', $item->payment_status) }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('payment_type') }}</th>
            <td>{{ \App\Models\Trade::tr('payment_type', $item->payment_type) }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('payment_trade_id') }}</th>
            <td>{{ $item->payment_trade_id }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('payment_money') }}</th>
            <td>{{ to_float($item->payment_money) }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('payment_at') }}</th>
            <td>{{ $item->payment_at }}</td>
        </tr>

        @if($item->refund_status != \App\Models\Trade::RefundStatus_None)
        <tr>
            <th>{{ \App\Models\Trade::tr('refund_status') }}</th>
            <td>{{ \App\Models\Trade::tr('refund_status', $item->refund_status) }}</td>
            <td>aaa</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('refund_money') }}</th>
            <td>{{ to_float($item->refund_money) }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('refund_remark') }}</th>
            <td>{{ $item->refund_remark }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('refund_at') }}</th>
            <td>{{ $item->refund_at }}</td>
        </tr>
        @endif

        @if($item->withdraw_status = \App\Models\Trade::WithdrawStatus_None)
        <tr>
            <th>{{ \App\Models\Trade::tr('withdraw_status') }}</th>
            <td>{{ \App\Models\Trade::tr('withdraw_status', $item->withdraw_status) }}</td>
        </tr>

        @if ($item->withdraw_id > 0)
        <tr>
            <th>{{ \App\Models\Trade::tr('withdraw_id') }}</th>
            <td>{{ $item->withdraw_id }}</td>
            <td>
                <a href="{{ route('withdraw.show', $item->withdraw_id) }}">{{ $item->withdraw_id }}</a>
            </td>
        </tr>
        @endif

        <tr>
            <th>{{ \App\Models\Trade::tr('withdraw_money') }}</th>
            @if($item->withdraw_money > 0)
            <td>
                <button class="btn btn-link btn-sm">
                    <a href="{{ route('cashes.show', optional($item->cashes)->id) }}">{{ $item->withdraw_money }}</a>
                </button>
            </td>
            @else
                <td>{{ to_float($item->withdraw_money) }}</td>
            @endif
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('platform_money') }}</th>
            <td>{{ to_float($item->platform_money) }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('platform_fee_rate') }}</th>
            <td>{{ to_float($item->platform_fee_rate, 3) }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('withdraw_at') }}</th>
            <td>{{ $item->withdraw_at }}</td>
        </tr>
        @endif

        @if($item->payment_type === \App\Models\Trade::PaymentType_IcCard)
        <tr>
            <th>{{ \App\Models\Trade::tr('card_id') }}</th>
            <td>{{ $item->card_id }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('card_pid') }}</th>
            <td>{{ $item->card_pid }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('card_money') }}</th>
            <td>{{ $item->card_money }}</td>
        </tr>
        @endif

        <tr>
            <th>{{ \App\Models\Trade::tr('created_at') }}</th>
            <td>{{ $item->created_at }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Trade::tr('updated_at') }}</th>
            <td>{{ $item->updated_at }}</td>
        </tr>

        @if(isset($item->deleted_at))
        <tr>
            <th>{{ \App\Models\Trade::tr('deleted_at') }}</th>
            <td>{{ $item->deleted_at }}</td>
        </tr>
        @endif

        </tbody>
    </table>

    <div>
        @if ($item->isCanRefund())
            <a href="{{ route('showRefundMoney', ['trade_id' => $item['id']]) }}" class="btn-block btn-danger">
                <h3>退款给用户</h3>
            </a>
        @else
            <h3>该订单不可退款(只有满足以下条件的订单才可以退款)</h3>
            <ul>
                <li>使用微信或者支付宝</li>
                <li>订单已付款</li>
                <li>订单没有提现</li>
                <li>一个订单只能退款一次。</li>
            </ul>
        @endif
    </div>

@else
    <h3>无记录</h3>
@endif
@endsection
