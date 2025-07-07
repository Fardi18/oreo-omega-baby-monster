@extends('_template_adm.master')

@php
    $module = ucwords(lang('system logs', $translations));
    $pagetitle = $module;
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
        </div>
        
        <div class="clearfix"></div>

        {{-- FILTER --}}
        <div class="row">
            <div class="col-md-3 col-sm-12 col-xs-12">
                <div class="control-group">
                    <div class="controls">
                        <div class="input-prepend input-group">
                            <span class="add-on input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" id="daterangepicker" class="form-control" value="" autocomplete="off" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9 col-sm-12 col-xs-12">
                <div class="control-group">
                    <div class="controls">
                        <div class="input-group">
                            <button class="btn btn-round btn-default" onclick="reset_filter()">
                                <i class="fa fa-refresh"></i> {{ ucwords(lang('reset', $translations)) }}
                            </button>
                            <button class="btn btn-round btn-info" onclick="confirm_export()">
                                <i class="fa fa-download"></i> {{ ucwords(lang('export', $translations)) }}
                            </button>
                            <button class="btn btn-round btn-primary" onclick="confirm_export_all()">
                                <i class="fa fa-download"></i> {{ ucwords(lang('export all', $translations)) }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
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
                                        <th>{{ ucwords(lang('log ID', $translations)) }}</th>
                                        <th>{{ ucwords(lang('user', $translations)) }}</th>
                                        <th>{{ ucwords(lang('activity', $translations)) }}</th>
                                        <th>{{ strtoupper(lang('url', $translations)) }}</th>
                                        <th>{{ lang('IP Address', $translations) }}</th>
                                        <th>{{ ucwords(lang('user agent', $translations)) }}</th>
                                        <th>{{ ucwords(lang('timestamp', $translations)) }}</th>
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
    <!-- bootstrap-daterangepicker -->
    @include('_vendors.daterangepicker.css')
@endsection

@section('script')
    <!-- DataTables -->
    @include('_vendors.datatables.script')
    <!-- bootstrap-daterangepicker -->
    @include('_vendors.daterangepicker.script')

    <script>
        $(document).ready(function() {
            init_daterangepicker_custom("daterangepicker", "DD/MM/YYYY", "", true, true, "01/08/2022", '', 0);

            $('#daterangepicker').on('change', function() {
                refresh_data();
                $(this).blur();
            });

            refresh_data();
        });

        function reset_filter() {
            $('#daterangepicker').val('');
            refresh_data();
        }

        function refresh_data() {
            var daterange = $('#daterangepicker').val();
            if (typeof daterange == 'undefined') {
                daterange = '';
            }

            @php
                // get table system name
                $table_log = (new \App\Models\log())->getTable();
                $table_log_detail = (new \App\Models\log_detail())->getTable();
                $table_module = (new \App\Models\module())->getTable();
                $table_admin = (new \App\Models\admin())->getTable();
            @endphp
            
            $('#datatables').show();
            $('#datatables').dataTable().fnDestroy();
            var table = $('#datatables').DataTable({
                orderCellsTop: true,
                fixedHeader: false,
                serverSide: true,
                processing: true,
                searching: true,
                ajax: "{{ route('admin.system_logs.get_data') }}?daterange="+daterange,
                order: [[ 0, 'desc' ]],
                columns: [
                    {data: 'id', name: '{{ $table_log }}.id'},
                    {data: 'username', name: '{{ $table_admin }}.username'},
                    {data: 'activity', name: '{{ $table_log_detail }}.action'},
                    {data: 'url', name: '{{ $table_log }}.url'},
                    {data: 'ip_address', name: '{{ $table_log }}.ip_address'},
                    {data: 'user_agent', name: '{{ $table_log }}.user_agent'},
                    {data: 'timestamp', name: '{{ $table_log }}.created_at'},
                    {data: 'action', name: 'action'},
                ]
            });
        }

        function confirm_export() {
            if (confirm("{{ lang('Are you sure to export this #item data?', $translations, ['#item' => $module]) }}")) {
                var daterange = $('#daterangepicker').val();
                if (typeof daterange == 'undefined') {
                    daterange = '';
                }

                window.location.href = "{{ route('admin.system_logs.export') }}?daterange="+daterange;
            }
        }

        function confirm_export_all() {
            if (confirm("{{ lang('Are you sure to export all #item data?', $translations, ['#item' => $module]) }}")) {
                window.location.href = "{{ route('admin.system_logs.export') }}";
            }
        }
    </script>
@endsection