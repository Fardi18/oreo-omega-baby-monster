@extends('_template_adm.master')

@php
    $module = ucwords(lang('module', $translations));
    $pagetitle = $module;
    $function_get_data = 'refresh_data();';

    if (isset($deleted_data)) {
        $pagetitle = ucwords(lang('deleted #item', $translations, ['#item' => $module]));
        $function_get_data = 'refresh_deleted_data();';
    }
@endphp

@section('title', $pagetitle)

@section('content')
    <div class="">
        {{-- display response message --}}
        @include('_template_adm.message')

        <div class="page-title">
            <div class="title_left">
                <h3>{{ $pagetitle }}</h3>
            </div>
            
            @if (isset($deleted_data))
                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right">
                        <a href="{{ route('admin.module') }}" class="btn btn-round btn-primary" style="float: right;">
                            <i class="fa fa-check-circle"></i>&nbsp; {{ ucwords(lang('active items', $translations)) }}
                        </a>
                    </div>
                </div>
            @else
                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right">
                        <a href="{{ route('admin.module.deleted_data') }}" class="btn btn-round btn-danger" style="float: right; margin-bottom: 5px;" data-toggle="tooltip" title="{{ ucwords(lang('view deleted items', $translations)) }}">
                            <i class="fa fa-trash"></i>
                        </a>
                        <a href="{{ route('admin.module.create') }}" class="btn btn-round btn-success" style="float: right;">
                            <i class="fa fa-plus-circle"></i>&nbsp; {{ ucwords(lang('add new', $translations)) }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="clearfix"></div>
        
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>{{ ucwords(lang('data list', $translations)) }}</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="table-responsive">
                            <table id="datatables" class="table table-striped table-bordered dt-responsive" style="display:none">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{{ ucwords(lang('module', $translations)) }}</th>
                                        <th>{{ ucwords(lang('status', $translations)) }}</th>
                                        <th>{{ ucwords(lang('created', $translations)) }}</th>
                                        <th>{{ ucwords(lang('last updated', $translations)) }}</th>
                                        <th>{{ ucwords(lang('action', $translations)) }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <table id="datatables-deleted" class="table table-striped table-bordered dt-responsive" style="display:none">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{{ ucwords(lang('module', $translations)) }}</th>
                                        <th>{{ ucwords(lang('created', $translations)) }}</th>
                                        <th>{{ ucwords(lang('deleted', $translations)) }}</th>
                                        <th>{{ ucwords(lang('action', $translations)) }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <!-- DataTables -->
    @include('_vendors.datatables.css')
@endsection

@section('script')
    <!-- DataTables -->
    @include('_vendors.datatables.script')

    <script>
        $(document).ready(function() {
            {{ $function_get_data }}
        });

        function refresh_data() {
            $('#datatables').show();
            $('#datatables').dataTable().fnDestroy();
            var table = $('#datatables').DataTable({
                orderCellsTop: true,
                fixedHeader: false,
                serverSide: true,
                processing: true,
                ajax: "{{ route('admin.module.get_data') }}",
                order: [[ 0, 'desc' ]],
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'status', name: 'status'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'action', name: 'action'},
                ],
                dom : 'Blfrtip',
                buttons: [
                    {
                        "extend": 'copy',
                        "text": '<i class="fa fa-files-o" style="color: green;"></i>',
                        "titleAttr": 'Copy',                               
                        "action": newexportaction
                    },
                    {
                        "extend": 'excel',
                        "text": '<i class="fa fa-file-excel-o" style="color: green;"></i>',
                        "titleAttr": 'Excel',                               
                        "action": newexportaction
                    },
                    {
                        "extend": 'csv',
                        "text": '<i class="fa fa-file-text-o" style="color: green;"></i>',
                        "titleAttr": 'CSV',                               
                        "action": newexportaction
                    },
                    {
                        "extend": 'pdf',
                        "text": '<i class="fa fa-file-pdf-o" style="color: green;"></i>',
                        "titleAttr": 'PDF',                               
                        "action": newexportaction
                    },
                    {
                        "extend": 'print',
                        "text": '<i class="fa fa-print" style="color: green;"></i>',
                        "titleAttr": 'Print',                                
                        "action": newexportaction
                    }
                ],
            });
        }

        function refresh_deleted_data() {
            $('#datatables-deleted').show();
            $('#datatables-deleted').dataTable().fnDestroy();
            var table = $('#datatables-deleted').DataTable({
                orderCellsTop: true,
                fixedHeader: false,
                serverSide: true,
                processing: true,
                ajax: "{{ route('admin.module.get_deleted_data') }}",
                order: [[ 0, 'asc' ]],
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'deleted_at', name: 'deleted_at'},
                    {data: 'action', name: 'action'},
                ]
            });
        }
    </script>
@endsection