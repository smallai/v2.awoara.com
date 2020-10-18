@extends('layouts.admin')

@section('content')

    <div class="row justify-content-md-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h3>退款操作</h3>
                    <div>
                        <span>设备: {{ $device['name'] }}</span>
                    </div>
                </div>

                <div class="card-body">

                    <form action="{{ route('refundMoney') }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('POST') }}

                        <input type="hidden" name="trade_id" value="{{ $trade['id'] }}">

                        {{--退款金额--}}
                        <div class="row form-group">
                            <label for="money" class="col-sm-2" style="text-align: right">退款金额:</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="money" name="money" value="{{ to_float($trade['payment_money']) }}" placeholder="请输入退款金额">
                            </div>
                        </div>

                        {{--退款原因--}}
                        <div class="row form-group">
                            <label for="reason" class="col-sm-2" style="text-align: right">退款原因(用户可见):</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="reason" name="reason" value="{{ old('reason') ?? '萌芽洗车退款' }}" placeholder="请输入退款原因">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="delete_vip_card" class="col-sm-2" style="text-align: right">同时删除会员卡:</label>
                            <div class="col-sm-8">
                                <input type="checkbox" name="delete_vip_card" value="1" checked>同时删除会员卡
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-primary btn-block">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

