@extends('layouts.admin')

@section('content')
    @if(count($devices) > 0)
    <div class="row justify-content-md-center">
        <div class="col-md-10">
            <div class="card">
                {{--<div class="card-header">创建套餐</div>--}}
                <div class="card-body">
                    <form role="form" action="{{ route('goods.store') }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('POST') }}

                        <div class="row form-group">
                            <label for="device_id" class="col-sm-2 text-right">设备:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="device_id" name="device_id">
                                    @foreach($devices as $device)
                                        <option value="{{ $device->id }}">{{ $device->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="name" class="col-sm-2 text-right">名称:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="请输入套餐名称,必须正确填写">
                            </div>
                        </div>

                        {{--<div class="row form-group">--}}
                            {{--<label for="image" class="col-sm-2 text-right">图片:</label>--}}
                            {{--<div class="col-sm-8">--}}
                                {{--<input type="file" class="form-control" id="image" name="image" value="{{ old('image') }}">--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="row form-group">
                            <label for="price" class="col-sm-2 text-right">价格(分):</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="price" name="price" value="{{ old('price') }}" placeholder="请输入价格(以分为单位，如100表示1元)，必须正确填写">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="seconds" class="col-sm-2 text-right">时间:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="seconds" name="seconds" value="{{ old('seconds') }}" placeholder="请输入可用时间,单位秒，如120秒对应2分钟">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="seconds" class="col-sm-2 text-right">属性:</label>
                            {{--<div class="col-sm-4">--}}
                                {{--<label>--}}
                                    {{--<input type="hidden" name="is_sale" value="0">--}}
                                    {{--<input type="checkbox" name="is_sale" value="{{ old('is_sale') ?? 1 }}">上架--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{--<div class="col-sm-4">--}}
                                {{--<label>--}}
                                    {{--<input type="hidden" name="is_recommend" value="0">--}}
                                    {{--<input type="checkbox" name="is_recommend" value="{{ old('is_recommend') ?? 0 }}">默认选中--}}
                                {{--</label>--}}
                            {{--</div>--}}

                            <div class="col-sm-4">
                                <label>
                                    <input type="hidden" name="is_sale" value="0">
                                    <input type="checkbox" name="is_sale" value="1" checked>上架
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label>
                                    <input type="hidden" name="is_recommend" value="0">
                                    <input type="checkbox" name="is_recommend" value="1" checked>默认选中
                                </label>
                            </div>
                        </div>

                        {{--@role('superadmin')--}}
                        <div class="row form-group">
                            <label for="count" class="col-sm-2 text-right">总次数:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="count" name="count" value="{{ old('count') ?? 1 }}" placeholder="请输入可用次数">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="address" class="col-sm-2" style="text-align: right">当天最多可用次数:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="address" name="today_limit" value="{{ old('today_limit') ?? 1 }}" placeholder="请输入会员卡当天最多使用次数。">
                            </div>
                        </div>

                        <div class="row form-group">
                            <label for="days" class="col-sm-2 text-right">有效期:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="days" name="days" value="{{ old('days') ?? 1 }}" placeholder="有效期，购买后需要在多少天内使用">
                            </div>
                        </div>
                        {{--@else--}}
                            {{--<input type="hidden" class="form-control" id="count" name="count" value="1" placeholder="请输入可用次数">--}}
                            {{--<input type="hidden" class="form-control" id="days" name="days" value="1" placeholder="有效期，购买后需要在多少天内使用">--}}
                        {{--@endrole--}}

                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-primary btn-block">提交</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    @else
        <h3>无权限</h3>
    @endif
@endsection

