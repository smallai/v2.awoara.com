@if(count($items) > 0)
    <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
        {{--<fieldset>--}}
            <legend style="text-align: center">设备浏览</legend>
        {{--</fieldset>--}}
        <thead>
        <tr>
            <th>网点</th>
            <th>订单</th>
            <th>金额</th>
            <th>网络</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->name }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $items->links() }}
@else
    <h3>无记录</h3>
@endif


