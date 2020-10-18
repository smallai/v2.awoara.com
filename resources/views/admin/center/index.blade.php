@extends('layouts.admin')

@section('content')
    <div>
        <form class="form-inline" id="form" href="{{ route('center.index') }}">

            <label for="device_id">选择设备</label>
            <select class="form-control" id="device_id" name="device_id" onchange="submitForm()">
                <option value="" {{ null == $device ? 'selected' : '' }}>所有设备</option>
                @foreach($devices as $curDevice)
                    <option value="{{ $curDevice->id }}" {{ $curDevice->id == optional($device)->id ? 'selected' : '' }}>{{ $curDevice->name }}</option>
                @endforeach
            </select>

            <label for="month">选择月份</label>
            <select class="form-control" id="month" name="month" onchange="submitForm()">
                @for($i=0; $i<12; $i++)
                    <option value="{{ \Carbon\Carbon::now()->addMonth(-$i)->format('Y-m') }}"
                            {{ $month == \Carbon\Carbon::now()->addMonth(-$i)->format('Y-m') ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::now()->addMonth(-$i)->format('Y-m') }}
                    </option>
                @endfor
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

    @if(count($items) > 0)
        <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
            {{--<fieldset>--}}
                <legend style="text-align: center">{{ $device ? $device->name : '所有设备' }} {{ $month }} 运营情况</legend>
                <div class="text-center">合计：
                    <span>订单： <span class="text-danger">{{ $items->sum('payment_count') }}</span></span>
                    <span>用户： <span class="text-danger">{{ $items->sum('washcar_count') }}</span></span>
                    <span>收款： <span class="text-danger">{{ to_float($items->sum('payment_money')) }}</span></span>
                    <span>退款： <span class="text-danger">{{ to_float($items->sum('refund_money') ) }}</span></span>
                    <span>可提现： <span class="text-danger">{{ to_float($items->sum('withdraw_money')) }}</span></span>
                    @role('superadmin')
                    <span>平台费： <span class="text-danger">{{ to_float($items->sum('platform_money')) }}</span></span>
                    @endrole
                    @if($items->sum('washcar_count') > 0)
                        <span>人均消费： <span class="text-danger">{{ to_float($items->sum('payment_money')/$items->sum('washcar_count')) }}</span></span>
                    @endif
                    @if($items->sum('payment_count') > 0)
                        <span>订单均价： <span class="text-danger">{{ to_float($items->sum('payment_money')/$items->sum('payment_count')) }}</span></span>
                    @endif
                </div>
            {{--</fieldset>--}}
            <thead>
            <tr>
                <th>日期</th>
                <th>订单</th>
                <th>客户</th>
                <th>收款</th>
                <th>退款</th>
                <th>可提现</th>
                {{--@role('superadmin')--}}
                <th>平台费</th>
                {{--@endrole--}}
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['date'] }}</td>
                    <td>{{ $item['payment_count'] }}</td>
                    <td>{{ $item['washcar_count'] }}</td>
                    <td>{{ to_float($item['payment_money']) }}</td>
                    <td>{{ to_float($item['refund_money']) }}</td>
                    <td>{{ to_float($item['withdraw_money']) }}</td>
                    {{--@role('superadmin')--}}
                    <td>{{ to_float($item['platform_money']) }}</td>
                    {{--@endrole--}}
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <h3>无记录</h3>
    @endif
@endsection

