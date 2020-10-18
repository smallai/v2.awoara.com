@extends('layouts.admin')

@section('content')
    <table class="table table-bordered" id="users-table">
    </table>
@stop

@push('scripts')
    <script>
        $(function() {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('user.dataTableData') !!}',
                columns: [
                    { title: '编号', data: 'id', name: 'id', render: (data) => `<span style="color: red">${data}</span>`},
                    { title: '名称', data: 'name', name: 'name' },
                    { title: '邮箱', data: 'email', name: 'email' },
                    { title: '创建时间', data: 'created_at', name: 'created_at', searchable: false},
                    { title: '更新时间', data: 'updated_at', name: 'updated_at', searchable: false }
                ],
                language: { //自定义描述....
                    "sProcessing": "正在获取数据, 请稍后...",
                    "sLengthMenu": "显示 _MENU_ 条",
                    "sZeroRecords": "没有找到数据",
                    "sInfo": "第_START_ - _END_项 &nbsp;&nbsp;&nbsp;&nbsp;共 _TOTAL_ 条",
                    "sInfoEmpty": "记录数为0",
                    "sInfoFiltered": "(全部记录数 _MAX_ 条)",
                    "sInfoPostFix": "",
                    "sSearch": "全局搜索",
                    "sUry": "",
                    "oPaginate": {
                        "sFirst": "第一页",
                        "sPrevious": "上一页",
                        "sNext": "下一页",
                        "sLast": "最后一页"
                    },
                    "loadingRecords": "Please wait - loading...",
                    "processing": "DataTables is currently busy",
                    "search": "Apply filter _INPUT_ to table"
                }
            });
        });
    </script>
@endpush
