{{-- ADD HTML SMALL MODAL - BEGIN --}}
@extends('_template_adm.modal_small')
{{-- SMALL MODAL CONFIG --}}
@section('small_modal_id', 'modal_import')
@section('small_modal_title', ucwords(lang('import', $translations)))
@section('small_modal_content')
  <label>{{ lang('Browse the file', $translations) }}</label>
  <div class="form-group">
    <input type="file" name="file" required="required" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
  </div>
@endsection
@section('small_modal_btn_label', ucwords(lang('import', $translations)))
@section('small_modal_form', true)
@section('small_modal_method', 'POST')
@section('small_modal_url', route('admin.phrase.import'))
@section('small_modal_form_validation', 'return validate_import_file()')
@section('small_modal_script')
    <script>
        function validate_import_file() {
            if (confirm("{{ lang('Are you sure to import this file?', $translations) }}")) {
                $('#modal_import').modal('hide');
                setTimeout(function(){ show_loading(); }, 500);
                return true;
            }
            return false;
        }
    </script>
@endsection
{{-- ADD HTML SMALL MODAL - END --}}

@extends('_template_adm.master')

@php
    use App\Libraries\Helper;

    $app_module = "Phrase";
    $module = ucwords(lang('phrase', $translations));
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
            
            @if (Helper::authorizing($app_module, 'Restore')['status'] == 'true')
                @if (isset($deleted_data))
                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right">
                            <a href="{{ route('admin.phrase') }}" class="btn btn-round btn-primary" style="float: right;">
                                <i class="fa fa-check-circle"></i>&nbsp; {{ ucwords(lang('active items', $translations)) }}
                            </a>
                        </div>
                    </div>
                @else
                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right">
                            <a href="{{ route('admin.phrase.deleted_data') }}" class="btn btn-round btn-danger" style="float: right; margin-bottom: 5px;" data-toggle="tooltip" title="{{ ucwords(lang('view deleted items', $translations)) }}">
                                <i class="fa fa-trash"></i>
                            </a>
                            <a href="{{ route('admin.phrase.create') }}" class="btn btn-round btn-success" style="float: right;">
                                <i class="fa fa-plus-circle"></i>&nbsp; {{ ucwords(lang('add new', $translations)) }}
                            </a>
                        </div>
                    </div>
                @endif
            @endif
        </div>
        
        <div class="clearfix"></div>

        {{-- TOOLBAR --}}
        <div class="row">
            <div class="col-md-9 col-sm-9 col-xs-9">
                <div class="control-group">
                    <div class="controls">
                        <div class="input-group">
                            @if (Helper::authorizing($app_module, 'Import')['status'] == 'true')
                                <button class="btn btn-round btn-primary" data-toggle="modal" data-target="#modal_import">
                                    <i class="fa fa-upload"></i> {{ ucwords(lang('import', $translations)) }}
                                </button>
                            @endif

                            @if (Helper::authorizing($app_module, 'Export')['status'] == 'true')
                                <button class="btn btn-round btn-warning" onclick="confirm_export()">
                                    <i class="fa fa-download"></i> {{ ucwords(lang('export', $translations)) }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @if (Helper::authorizing($app_module, 'Truncate')['status'] == 'true')
                <div class="col-md-3 col-sm-3 col-xs-3">
                    <div class="control-group">
                        <div class="controls">
                            <div class="input-group" style="float: right;">
                                <form action="{{ route('admin.phrase.truncate') }}" method="post" style="display: inline;" onsubmit="return confirm('{{ lang('Are you sure to truncate/delete all #item? data', $translations, ['#item' => $module]) }}\n({{ lang('This action cannot be undone', $translations) }})')">
                                    @csrf
                                    <button type="submit" class="btn btn-round btn-danger">
                                        <i class="fa fa-close"></i> {{ ucwords(lang('truncate', $translations)) }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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
                            <table id="datatables" class="table table-striped table-bordered dt-responsive" style="display:none">
                                <thead>
                                    <tr>
                                        <th>{{ ucwords(lang('phrase', $translations)) }}</th>
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
                                        <th>{{ ucwords(lang('phrase', $translations)) }}</th>
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
                ajax: "{{ route('admin.phrase.get_data') }}",
                order: [[ 0, 'asc' ]],
                columns: [
                    {data: 'content', name: 'content'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'action', name: 'action'},
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
                ajax: "{{ route('admin.phrase.get_deleted_data') }}",
                order: [[ 0, 'asc' ]],
                columns: [
                    {data: 'content', name: 'content'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'deleted_at', name: 'deleted_at'},
                    {data: 'action', name: 'action'},
                ]
            });
        }

        function confirm_export() {
            if (confirm("{{ lang('Are you sure to export all #item data?', $translations, ['#item' => $module]) }}")) {
                window.location.href = "{{ route('admin.phrase.export') }}";
            }
        }
    </script>
@endsection