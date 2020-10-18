@extends('layouts.admin')

@section('content')
    @if(isset($item))
        <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
        <filedset>
            <legend style="text-align: center">商品详情</legend>
        </filedset>
        <tbody>
        <tr>
            <th>{{ \App\Models\Goods::tr('id') }}</th>
            <td>{{ $item->id }}</td>
        </tr>
        <tr>
            <th>{{ \App\Models\Goods::tr('name') }}</th>
            <td>{{ $item->name }}</td>
        </tr>
        <tr>
            <th>{{ \App\Models\Goods::tr('price') }}</th>
            <td>{{ to_float($item->price) }}</td>
        </tr>
        <tr>
            <th>{{ \App\Models\Goods::tr('image') }}</th>
            <td><img src = "{{ $item->image }}" class="img-thumbnail" ></td>
        </tr>
        <tr>
            <th>{{ \App\Models\Goods::tr('is_sale') }}</th>
            <td>{{ \App\Models\Goods::tr('is_sale', $item->is_sale)  }}</td>
        </tr>

        @role('superadmin')
        <tr>
            <th>{{ \App\Models\Goods::tr('is_recommend') }}</th>
            <td>{{ \App\Models\Goods::tr('is_recommend', $item->is_recommend) }}</td>
        </tr>
        <tr>
            <th>{{ \App\Models\Goods::tr('seconds') }}</th>
            <td>{{ to_time($item->seconds) }}</td>
        </tr>
        <tr>
            <th>{{ \App\Models\Goods::tr('count') }}</th>
            <td>{{ $item->count }}</td>
        </tr>
        <tr>
            <th>{{ \App\Models\Goods::tr('days') }}</th>
            <td>{{ $item->days }}</td>
        </tr>
        @endrole

        <tr>
            <th>{{ \App\Models\Goods::tr('created_at') }}</th>
            <td>{{ $item->created_at }}</td>
        </tr>

        <tr>
            <th>{{ \App\Models\Goods::tr('updated_at') }}</th>
            <td>{{ $item->updated_at }}</td>
        </tr>

        @if (isset($item->deleted_at))
            <tr>
                <th>{{ \App\Models\Goods::tr('deleted_at') }}</th>
                <td>{{ $item->deleted_at }}</td>
            </tr>
        @endif

        </tbody>
    </table>

        @if (\Illuminate\Support\Facades\Auth::user()->hasRole('superadmin') || ( $item->owner_id === \Illuminate\Support\Facades\Auth::user()->id))
        <div>
            <a href="#" onclick="delete_goods({{ $item->id }})" class="btn-block btn-danger">
                <h3>删除这个套餐</h3>
            </a>
            <form id="delete_goods_form_{{ $item->id }}" action="{{ route('goods.destroy', $item->id) }}" method="POST" style="display: inline">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
            </form>

            <script>
                function delete_goods(id)
                {
                    $form = $('#delete_goods_form_' + id);
                    $form.submit();
                    return true;
                }
            </script>
        </div>
        @endif

    @else
    <h3>无记录</h3>
    @endif
@endsection

