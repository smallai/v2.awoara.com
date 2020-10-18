@extends('layouts.admin')

@section('content')

    @if (Auth::user()->isSuperAdmin())
        <div>
            <form class="form-inline" id="form" href="{{ route('withdraw.index') }}">

                <label for="owner_id">选择提现人</label>
                <select class="form-control" id="owner_id" name="owner_id" onchange="submitForm()">
                    <option value="" {{ !isset($owner_id) ? 'selected="selected"' : '' }}>
                        全部
                    </option>
                    @foreach($users as $user)
                        <option value="{{ $user['id'] }}" {{ $owner_id == $user['id'] ? 'selected="selected"' : '' }}>
                            <span>{{ $user['real_name'] }}</span>
                            (<span>{{ $user['phone'] }}</span>)
                        </option>
                    @endforeach
                </select>
                {{--<button type="submit" class="btn btn-primary">提交</button>--}}
            </form>
            <script>
                function submitForm()
                {
                    form = $('#form');
                    form.submit();
                }
            </script>
        </div>
    @endif

    @if(count($items) > 0)
        <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
            <filedset>
                <legend style="text-align: center">提现记录</legend>
            </filedset>
            <thead>
                <tr>
                    <th>{{ \App\Models\WithdrawMoney::tr('id') }}</th>
                    <th>{{ \App\Models\WithdrawMoney::tr('device_id') }}</th>
                    <th>{{ \App\Models\WithdrawMoney::tr('withdraw_money') }}</th>
                    <th>{{ \App\Models\WithdrawMoney::tr('withdraw_status') }}</th>
                    <th>{{ \App\Models\WithdrawMoney::tr('withdraw_at') }}</th>

                    @role('superadmin')
                    <th>{{ \App\Models\WithdrawMoney::tr('payment_money') }}</th>
                    <th>{{ \App\Models\WithdrawMoney::tr('refund_money') }}</th>
                    <th>{{ \App\Models\WithdrawMoney::tr('withdraw_money') }}</th>
                    <th>{{ \App\Models\WithdrawMoney::tr('platform_money') }}</th>
                    <th>{{ \App\Models\WithdrawMoney::tr('platform_fee_rate') }}</th>
                    @endrole
                    <th>操作</th>
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
                        <a href="{{ route('withdraw.show', $item->id) }}">{{ $item->id }}</a>
                    </td>
                    <td>
                        <a href="{{ route('device.show', $item->device_id) }}">{{ optional($item->device)->name }}</a>
                    </td>
                    <td>{{ to_float($item->withdraw_money )}}</td>
                    <td>{{ \App\Models\WithdrawMoney::tr('withdraw_status', $item->withdraw_status) }}</td>
                    <td>{{ optional($item->withdraw_at)->format('m-d H:i') }}</td>

                    @role('superadmin')
                    <td>{{ to_float($item->payment_money) }}</td>
                    <td>{{ to_float($item->refund_money) }}</td>
                    <td>{{ to_float($item->withdraw_money) }}</td>
                    <td>{{ to_float($item->platform_money) }}</td>
                    <td>{{ to_float($item->platform_fee_rate, 3) }}</td>
                    @endrole

                    <td>
                        <a href="{{ route('withdraw.show', $item->id) }}">详情</a>


                        @if ($item['withdraw_status'] !== App\Models\Trade::WithdrawStatus_Success)
                            @role('superadmin')
                                <a onclick="event.preventDefault();document.getElementById('form-{{ $item->id }}').submit();" class="text-danger">
                                    删除
                                </a>
                                <form id="form-{{ $item->id  }}" action="{{ route('withdraw.reset') }}" METHOD="POST" onclick="javascript;" style="display: inline;">
                                    {{ csrf_field() }}
                                    {{ method_field('POST') }}
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                </form>
                            @endrole
                        @endif

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $items->links() }}
    @else
        <h3>无记录</h3>
    @endif
@endsection
