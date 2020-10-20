@if (count($items) > 0)
    <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
        <filedset>
            <legend style="text-align: center">支付记录</legend>
        </filedset>
        <thead>
        <tr>
            <td>{{ \App\Models\Trade::tr('id') }}</td>
            <td>{{ \App\Models\Trade::tr('created_at') }}</td>
            <td>{{ \App\Models\Device::tr('name') }}</td>
            <td>{{ \App\Models\Trade::tr('payment_type') }}</td>
            @role('superadmin')
                <td>{{ \App\Models\Trade::tr('payment_money') }}</td>
                <td>{{ \App\Models\Trade::tr('refund_money') }}</td>
                <td>{{ \App\Models\Trade::tr('withdraw_money') }}</td>
                <td>{{ \App\Models\Trade::tr('platform_money') }}</td>
                <td>{{ \App\Models\Trade::tr('payment_status') }}</td>
                <td>{{ \App\Models\Trade::tr('goods_status') }}</td>
                <td>{{ \App\Models\Trade::tr('refund_status') }}</td>
                <td>{{ \App\Models\Trade::tr('withdraw_status') }}</td>
            @else
                <td>{{ \App\Models\Trade::tr('payment_money') }}</td>
                <td>{{ \App\Models\Trade::tr('refund_money') }}</td>
                <td>{{ \App\Models\Trade::tr('withdraw_money') }}</td>
                <td>{{ \App\Models\Trade::tr('payment_status') }}</td>
                <td>{{ \App\Models\Trade::tr('withdraw_status') }}</td>
            @endrole

            {{--<td>操作</td>--}}
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            @if ($item->hasError())
                <tr class="table-danger">
            @else
                <tr>
            @endif
                <td>
                    @if(($item['payment_type'] === \App\Models\Trade::PaymentType_Alipay) || ($item['payment_type'] === \App\Models\Trade::PaymentType_WeChat))
                    <a href="{{ route('trade.show', $item->id) }}">{{ $item->id }}</a>
                    @else
                        {{ $item->id }}
                    @endif
                </td>
                <td>{{ optional($item->created_at)->format('m-d H:i') }}</td>
                <td><a href="{{ route('device.show', $item->device_id) }}">{{ optional($item->device)->name }}</a></td>
                <td>{{ \App\Models\Trade::tr('payment_type', $item->payment_type) }}</td>

                @role('superadmin')
                    <td>{{ to_float($item->payment_money) }}</td>
                    <td>{{ to_float($item->refund_money) }}</td>
                    <td>{{ to_float($item->withdraw_money) }}</td>
                    <td>{{ to_float($item->platform_money) }}</td>
                    <td>{{ \App\Models\Trade::tr('payment_status', $item->payment_status) }}</td>
                    <td>{{ \App\Models\Trade::tr('confirm_status', $item->confirm_status) }}</td>
                    <td>{{ \App\Models\Trade::tr('refund_status', $item->refund_status) }}</td>
                    <td>{{ \App\Models\Trade::tr('withdraw_status', $item->withdraw_status) }}</td>
                @else
                    <td>{{ to_float($item->payment_money) }}</td>
                    <td>{{ to_float($item->refund_money) }}</td>
                    <td>{{ to_float($item->withdraw_money) }}</td>
                    <td>{{ \App\Models\Trade::tr('payment_status', $item->payment_status) }}</td>
                    <td>{{ \App\Models\Trade::tr('withdraw_status', $item->withdraw_status) }}</td>
                @endrole
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $items->links() }}
@else
    <h3>无记录</h3>
@endif

