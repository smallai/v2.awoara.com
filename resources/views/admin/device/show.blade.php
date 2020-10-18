@extends('layouts.admin')

<style>
    .middle {
        float: none;
        display: inline-block;
        vertical-align: middle;
    }
</style>

@section('content')
    @if(isset($item))
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover table-sm table-responsive-sm">
                {{--<fieldset>--}}
                    <legend style="text-align: center">设备详情</legend>
                {{--</fieldset>--}}

                <tbody>
                <tr>
                    <th>{{ \App\Models\Device::tr('id') }}</th>
                    <td>{{ $item->id }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('name') }}</th>
                    <td>{{ $item->name }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('is_online') }}</th>
                    {{--<td>{{ \App\Models\Device::tr('is_online', $item->is_online) }}</td>--}}
                    <td>
                        @if ($item['is_online'])
                            <span class="text-success">在线</span>
                        @else
                            <span class="text-danger">离线</span>
                        @endif
                    </td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('status') }}</th>
                    <td>{{ \App\Models\Device::tr('status', $item->status) }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('address') }}</th>
                    <td>{{ $item->address }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('phone') }}</th>
                    <td>{{ $item->phone }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('platform_fee_rate') }}</th>
                    <td>{{ to_float($item->platform_fee_rate, 3) }}</td>
                </tr>

                @if (Auth::user()->isSuperAdmin())
                <tr>
                    <th>{{ \App\Models\Device::tr('product_key') }}</th>
                    <td>{{ $item->product_key }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('device_name') }}</th>
                    <td>{{ $item->device_name }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('device_secret') }}</th>
                    <td>{{ $item->device_secret }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('company_logo') }}</th>
                    <td>{{ $item->company_logo }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('company_name') }}</th>
                    <td>{{ $item->company_name }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('company_address') }}</th>
                    <td>{{ $item->company_address }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('company_phone') }}</th>
                    <td>{{ $item->company_phone }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('company_site') }}</th>
                    <td>{{ $item->company_site }}</td>
                </tr>
                @endif

                <tr>
                    <th>{{ \App\Models\Device::tr('created_at') }}</th>
                    <td>{{ $item->created_at }}</td>
                </tr>

                <tr>
                    <th>{{ \App\Models\Device::tr('updated_at') }}</th>
                    <td>{{ $item->updated_at }}</td>
                </tr>

                @if (isset($item->deleted_at) && Auth::user()->isSuperAdmin())
                    <tr>
                        <th>{{ \App\Models\Device::tr('deleted_at') }}</th>
                        <td>{{ $item->deleted_at }}</td>
                    </tr>
                @endif

                </tbody>
            </table>

            @role('superadmin')
            <div style="align-content: center">
                <div class="btn-block btn-danger">
                    <a href="#" onclick="delete_item({{ $item->id }})">
                        <h3>删除这个设备</h3>
                    </a>
                </div>

                <form id="form_{{ $item->id }}" action="{{ route('device.destroy', $item->id) }}" method="POST" style="display: inline">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                </form>
            </div>
            @endrole

            <script>
                function delete_item(id)
                {
                    $form = $('#form_' + id);
                    $form.submit();
                    return true;
                }
            </script>
        </div>

    @else
    <h3>无记录</h3>
    @endif
@endsection

