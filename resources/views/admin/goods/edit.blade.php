@extends('layouts.admin')

@section('content')

    @role('admin|superadmin')
    @if (isset($item))
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <h3>修改商品配置</h3>
        </div>
    </div>

    <form action="{{ route('goods.update', $item->id) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div class="row form-group">
            <label for="name" class="col-sm-2 text-right">名称:</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="name" name="name" value="{{ $item->name  }}" placeholder="请输入套餐名称,必须正确填写">
            </div>
        </div>

        {{--<div class="row form-group">--}}
            {{--<label for="image" class="col-sm-2 text-right">图片:</label>--}}
            {{--<div class="col-sm-8">--}}
                {{--<input type="file" class="form-control" id="image" name="image" value="{{ $item->image }}">--}}
            {{--</div>--}}
        {{--</div>--}}

        <div class="row form-group">
            <label for="price" class="col-sm-2 text-right">价格(分):</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="price" name="price" value="{{ $item->price }}" placeholder="请输入价格(以分为单位，如100表示1元)，必须正确填写">
            </div>
        </div>

        <div class="row form-group">
            <label for="seconds" class="col-sm-2 text-right">时间:</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="seconds" name="seconds" value="{{ $item->seconds }}" placeholder="请输入可用时间,单位秒，如120秒对应2分钟">
            </div>
        </div>

        <div class="row form-group">
            <label for="seconds" class="col-sm-2 text-right">属性:</label>
            <div class="col-sm-4">
                <label>
                    <input type="hidden" name="is_sale" value="0">
                    <input type="checkbox" name="is_sale" value="1" {{ $item->is_sale ? 'checked' : '' }}>上架
                </label>
            </div>
            <div class="col-sm-4">
                <label>
                    <input type="hidden" name="is_recommend" value="0">
                    <input type="checkbox" name="is_recommend" value="1" {{ $item->is_recommend ? 'checked' : '' }}>默认选中
                </label>
            </div>
        </div>

{{--        @role('superadmin')--}}
            <div class="row form-group">
                <label for="count" class="col-sm-2 text-right">次数:</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="count" name="count" value="{{ $item->count }}" placeholder="请输入可用次数">
                </div>
            </div>

            <div class="row form-group">
                <label for="address" class="col-sm-2" style="text-align: right">当天可用次数:</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="address" name="today_limit" value="{{ $item->today_limit }}" placeholder="请输入会员卡当天最多使用次数。">
                </div>
            </div>

            <div class="row form-group">
                <label for="days" class="col-sm-2 text-right">有效期:</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="days" name="days" value="{{ $item->days }}" placeholder="有效期，购买后需要在多少天内使用">
                </div>
            </div>
{{--        @endrole--}}

        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <button type="submit" class="btn btn-primary btn-block">提交</button>
            </div>
        </div>

    </form>
    @endif
    @else
        <h3>无此权限</h3>
    @endrole

@endsection

