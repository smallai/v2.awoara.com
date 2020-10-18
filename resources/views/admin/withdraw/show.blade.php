@extends('layouts.admin')

@section('content')

    @if (isset($item))
        <table class="table table-bordered table-sm table-hover">
            <filedset>
                <legend>提现记录</legend>
            </filedset>
            <tbody>
                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('id') }}</th>
                    <td>{{ $item->id }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('device_id') }}</th>
                    <td>
                        <a href="{{ route('device.show', $item->device_id) }}">{{ optional($item->device)->name }}</a>
                    </td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('owner_id') }}</th>
                    <td>
                        <a href="{{ route('user.show', $item->owner_id) }}">{{ optional($item->owner)->name }}</a>
                    </td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('withdraw_status') }}</th>
                    <td>{{ \App\Models\WithdrawMoney::tr('withdraw_status', $item->withdraw_status) }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('withdraw_at') }}</th>
                    <td>{{ $item->withdraw_at }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('owner_ip') }}</th>
                    <td>
                        <a href="https://www.baidu.com/s?wd={{ $item->owner_ip }}">{{ $item->owner_ip }}</a>
                    </td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('owner_phone') }}</th>
                    <td>{{ $item->owner_phone }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('owner_email') }}</th>
                    <td>{{ $item->owner_email }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('owner_payee') }}</th>
                    <td>{{ $item->owner_payee }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('owner_real_name') }}</th>
                    <td>{{ $item->owner_real_name }}</td>
                </tr>

                @role('superadmin')
                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('payment_money') }}</th>
                    <td>{{ to_float($item->payment_money) }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('refund_money') }}</th>
                    <td>{{ to_float($item->refund_money) }}</td>
                </tr>
                @endrole

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('withdraw_money') }}</th>
                    <td>{{ to_float($item->withdraw_money) }}</td>
                </tr>

                @role('superadmin')
                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('platform_money') }}</th>
                    <td>{{ to_float($item->platform_money) }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('platform_fee_rate') }}</th>
                    <td>{{ to_float($item->platform_fee_rate, 3) }}</td>
                </tr>
                @endrole

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('payment_type') }}</th>
                    <td>{{ \App\Models\WithdrawMoney::tr('payment_type', $item->payment_type) }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('payer_show_name') }}</th>
                    <td>{{ $item->payer_show_name }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('payer_remark') }}</th>
                    <td>{{ $item->payer_remark }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('payment_trade_id') }}</th>
                    <td>{{ $item->payment_trade_id }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('payment_code') }}</th>
                    <td>{{ $item->payment_code }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('payment_msg') }}</th>
                    <td>{{ $item->payment_msg }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('created_at') }}</th>
                    <td>{{ $item->created_at }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('updated_at') }}</th>
                    <td>{{ $item->updated_at }}</td>
                </tr>

                @if (isset($item->deleted_at))
                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('deleted_at') }}</th>
                    <td>{{ $item->deleted_at }}</td>
                </tr>
                @endif

            </tbody>
        </table>
    @else
        <h3>无记录</h3>
    @endif

@endsection
