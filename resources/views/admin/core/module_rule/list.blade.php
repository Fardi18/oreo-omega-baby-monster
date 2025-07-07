@extends('_template_adm.master')

@php
    $module = ucwords(lang('rules', $translations));
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
                        <a href="{{ route('admin.module_rule') }}" class="btn btn-round btn-primary" style="float: right;">
                            <i class="fa fa-check-circle"></i>&nbsp; {{ ucwords(lang('active items', $translations)) }}
                        </a>
                    </div>
                </div>
            @else
                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right">
                        <a href="{{ route('admin.module_rule.deleted_data') }}" class="btn btn-round btn-danger" style="float: right; margin-bottom: 5px;" data-toggle="tooltip" title="{{ ucwords(lang('view deleted items', $translations)) }}">
                            <i class="fa fa-trash"></i>
                        </a>
                        <a href="{{ route('admin.module_rule.create') }}" class="btn btn-round btn-success" style="float: right;">
                            <i class="fa fa-plus-circle"></i>&nbsp; {{ ucwords(lang('add new', $translations)) }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="clearfix"></div>

        {{-- FILTER --}}
        <div class="row">
            {{-- filter by: modules --}}
            <div class="col-md-3 col-sm-12 col-xs-12">
                <div class="control-group">
                    <div class="controls">
                        <div class="input-prepend input-group">
                            <span class="add-on input-group-addon"><i class="fa fa-folder"></i></span>
                            <select style="width: 200px" id="filter_module" class="form-control select2">
                                <option value="" selected>- {{ strtoupper(lang('all', $translations) . ' ' . lang('modules', $translations)) }} -</option>
                                @if(isset($modules[0]))
                                    @foreach($modules as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>
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
                                        <th>{{ ucwords(lang('module', $translations)) }}</th>
                                        <th>{{ ucwords(lang('rule', $translations)) }}</th>
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
                                        <th>{{ ucwords(lang('module', $translations)) }}</th>
                                        <th>{{ ucwords(lang('rule', $translations)) }}</th>
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
    <!-- Select2 -->
    @include('_vendors.select2.css')
@endsection

@section('script')
    <!-- DataTables -->
    @include('_vendors.datatables.script')
    <!-- Select2 -->
    @include('_vendors.select2.script')

    <script>
        $(document).ready(function() {
            {{ $function_get_data }}

            $('#filter_module').on('change', function() {
                {{ $function_get_data }}
                $(this).blur();
            });
        });

        function refresh_data() {
            var filter_module = $('#filter_module').val();
            if (typeof filter_module == 'undefined') {
                filter_module = '';
            }

            $('#datatables').show();
            $('#datatables').dataTable().fnDestroy();
            var table = $('#datatables').DataTable({
                orderCellsTop: true,
                fixedHeader: false,
                serverSide: true,
                processing: true,
                ajax: "{{ route('admin.module_rule.get_data') }}?module="+filter_module,
                order: [[ 0, 'asc' ]],
                columns: [
                    {data: 'module_name', name: 'modules.name'},
                    {data: 'name', name: 'name'},
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
                ajax: "{{ route('admin.module_rule.get_deleted_data') }}",
                order: [[ 0, 'asc' ]],
                columns: [
                    {data: 'module_name', name: 'modules.name'},
                    {data: 'name', name: 'name'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'deleted_at', name: 'deleted_at'},
                    {data: 'action', name: 'action'},
                ]
            });
        }
    </script>
@endsection