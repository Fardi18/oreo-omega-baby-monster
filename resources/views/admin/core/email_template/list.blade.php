@extends('_template_adm.master')

@php
    $module = ucwords(lang('email template', $translations));
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
                        <a href="{{ route('admin.email_template') }}" class="btn btn-round btn-primary" style="float: right;">
                            <i class="fa fa-check-circle"></i>&nbsp; {{ ucwords(lang('active items', $translations)) }}
                        </a>
                    </div>
                </div>
            @else
                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right">
                        <a href="{{ route('admin.email_template.deleted_data') }}" class="btn btn-round btn-danger"
                            style="float: right; margin-bottom: 5px;" data-toggle="tooltip"
                            title="{{ ucwords(lang('view deleted items', $translations)) }}">
                            <i class="fa fa-trash"></i>
                        </a>
                        <a href="{{ route('admin.email_template.create') }}" class="btn btn-round btn-success"
                            style="float: right;">
                            <i class="fa fa-plus-circle"></i>&nbsp; {{ ucwords(lang('add new', $translations)) }}
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="clearfix"></div>

        {{-- FILTER --}}
        {{-- <div class="row"> --}}
        {{-- filter by: is_active atau status --}}
        {{-- <div class="col-md-3 col-sm-12 col-xs-12">
                <div class="control-group">
                    <div class="controls">
                        <div class="input-prepend input-group">
                            <span class="add-on input-group-addon"><i class="fa fa-check-square-o"></i></span>
                            <select style="width: 200px" id="filter_status" class="form-control select2">
                                <option value="" selected>-
                                    {{ strtoupper(lang('all', $translations) . ' ' . lang('status', $translations)) }} -
                                </option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>{{ ucwords(lang('data list', $translations)) }}</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="table-responsive">
                            <table id="datatables" class="table table-striped table-bordered" style="display:none">
                                <thead>
                                    <tr>
                                        <th>{{ ucwords(lang('unique name', $translations)) }}</th>
                                        <th>{{ ucwords(lang('subject', $translations)) }}</th>
                                        {{-- <th>{{ ucwords(lang('cc', $translations)) }}</th>
                                        <th>{{ ucwords(lang('bcc', $translations)) }}</th>
                                        <th>{{ ucwords(lang('reply to', $translations)) }}</th> --}}
                                        <th>{{ ucwords(lang('status', $translations)) }}</th>
                                        <th>{{ ucwords(lang('created', $translations)) }}</th>
                                        <th>{{ ucwords(lang('last updated', $translations)) }}</th>
                                        <th>{{ ucwords(lang('action', $translations)) }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <table id="datatables-deleted" class="table table-striped table-bordered" style="display:none">
                                <thead>
                                    <tr>
                                        <th>{{ ucwords(lang('unique name', $translations)) }}</th>
                                        <th>{{ ucwords(lang('subject', $translations)) }}</th>
                                        {{-- <th>{{ ucwords(lang('cc', $translations)) }}</th>
                                        <th>{{ ucwords(lang('bcc', $translations)) }}</th>
                                        <th>{{ ucwords(lang('reply to', $translations)) }}</th> --}}
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

            // Handle filter change event
            // $('#filter_status').on('change', function() {
            //     {!! $function_get_data !!}
            //     $(this).blur(); // Optional, to remove focus from the select after change
            // });
        });

        function refresh_data() {
            // var filterStatus = $('#filter_status').val();

            $('#datatables').show();
            $('#datatables').dataTable().fnDestroy();
            var table = $('#datatables').DataTable({
                orderCellsTop: true,
                fixedHeader: false,
                serverSide: true,
                processing: true,
                ajax: "{{ route('admin.email_template.get_data') }}",
                order: [
                    [3, 'desc']
                ],
                columns: [{
                        data: 'unique_name',
                        name: 'unique_name'
                    },
                    {
                        data: 'subject',
                        name: 'subject'
                    },
                    // {
                    //     data: 'cc',
                    //     name: 'cc'
                    // },
                    // {
                    //     data: 'bcc',
                    //     name: 'bcc'
                    // },
                    // {
                    //     data: 'reply_to',
                    //     name: 'reply_to'
                    // },
                    {
                        data: 'status_label',
                        name: 'is_active'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ]
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
                ajax: "{{ route('admin.email_template.get_deleted_data') }}",
                order: [
                    [3, 'desc']
                ],
                columns: [{
                        data: 'unique_name',
                        name: 'unique_name'
                    },
                    {
                        data: 'subject',
                        name: 'subject'
                    },
                    // {
                    //     data: 'cc',
                    //     name: 'cc'
                    // },
                    // {
                    //     data: 'bcc',
                    //     name: 'bcc'
                    // },
                    // {
                    //     data: 'reply_to',
                    //     name: 'reply_to'
                    // },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ]
            });
        }
    </script>
@endsection
