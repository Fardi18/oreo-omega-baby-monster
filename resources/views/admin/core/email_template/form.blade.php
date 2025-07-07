@extends('_template_adm.master')

@php
    use App\Libraries\Helper;

    $pagetitle = ucwords(lang('email template', $translations));
    if (isset($data)) {
        $pagetitle .= ' (' . ucwords(lang('edit', $translations)) . ')';
        $link = route('admin.email_template.update', $raw_id);
    } else {
        $pagetitle .= ' (' . ucwords(lang('new', $translations)) . ')';
        $link = route('admin.email_template.store');
        $data = null;
    }
@endphp

@section('title', $pagetitle)

@section('content')
    <div class="">
        <!-- message info -->
        @include('_template_adm.message')

        <div class="page-title">
            <div class="title_left">
                <h3>{{ $pagetitle }}</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <form id="form_data" class="form-horizontal form-label-left" action="{{ $link }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>{{ ucwords(lang('form details', $translations)) }}</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li style="float: right !important;"><a class="collapse-link"><iclass="fa fa-chevron-up"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br />

                            @php
                                // set_input_form($type, $input_name, $label_name, $data, $errors, $required = false, $config = null)
                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                $config->limit_chars = 50;
                                $config->info_text = '<br><i class="fa fa-info-circle"></i> This is to identify the email template, it must be unique, e.g. user-signup, user-forgot-password, user-reset-password';
                                $config->placeholder = 'Example: user-signup';
                                echo set_input_form(
                                    'text',
                                    'unique_name',
                                    ucwords(lang('unique name', $translations)),
                                    $data,
                                    $errors,
                                    true,
                                    $config,
                                );

                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                $config->limit_chars = 255;
                                $config->placeholder = 'Example: New Account';
                                echo set_input_form(
                                    'text',
                                    'subject',
                                    ucwords(lang('subject', $translations)),
                                    $data,
                                    $errors,
                                    true,
                                    $config,
                                );

                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                $config->info_text = '<i class="fa fa-info-circle"></i> Use a comma (,) without spaces to separate email addresses, e.g. mail1@domain.com,mail2@domain.com';
                                $config->placeholder = '';
                                echo set_input_form(
                                    'text',
                                    'cc',
                                    'CC',
                                    $data,
                                    $errors,
                                    false,
                                    $config,
                                );

                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                $config->info_text = '<i class="fa fa-info-circle"></i> Use a comma (,) without spaces to separate email addresses, e.g. mail1@domain.com,mail2@domain.com';
                                $config->placeholder = '';
                                echo set_input_form(
                                    'text',
                                    'bcc',
                                    'BCC',
                                    $data,
                                    $errors,
                                    false,
                                    $config,
                                );

                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                $config->info_text = '<i class="fa fa-info-circle"></i> Only 1 email address is allowed.';
                                $config->placeholder = '';
                                echo set_input_form(
                                    'text',
                                    'reply_to',
                                    'Reply-To',
                                    $data,
                                    $errors,
                                    false,
                                    $config,
                                );

                                echo set_input_form(
                                    'textarea',
                                    'email_body',
                                    ucwords(lang('email body', $translations)),
                                    $data,
                                    $errors,
                                    true,
                                );

                                $config = new \stdClass();
                                $config->default = 'checked';
                                echo set_input_form(
                                    'switch',
                                    'is_active',
                                    ucfirst(lang('status', $translations)),
                                    $data,
                                    $errors,
                                    false,
                                    $config,
                                );
                            @endphp

                            {{-- <div class="ln_solid"></div> --}}

                            @php
                                // only show when edit
                                if ($data) {
                                    $time_ago = Helper::time_ago(
                                        strtotime($data->created_at),
                                        lang('ago', $translations),
                                        Helper::get_periods($translations),
                                    );
                                    $config = new \stdClass();
                                    $config->attributes = 'readonly';
                                    $config->value = Helper::locale_timestamp($data->created_at) . ' - ' . $time_ago;
                                    echo set_input_form(
                                        'text',
                                        'created_at',
                                        ucwords(lang('created at', $translations)),
                                        $data,
                                        $errors,
                                        false,
                                        $config,
                                    );

                                    $time_ago = Helper::time_ago(
                                        strtotime($data->updated_at),
                                        lang('ago', $translations),
                                        Helper::get_periods($translations),
                                    );
                                    $config = new \stdClass();
                                    $config->attributes = 'readonly';
                                    $config->value = Helper::locale_timestamp($data->updated_at) . ' - ' . $time_ago;
                                    echo set_input_form(
                                        'text',
                                        'updated_at',
                                        ucwords(lang('last updated at', $translations)),
                                        $data,
                                        $errors,
                                        false,
                                        $config,
                                    );
                                }
                            @endphp

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                @php
                                    echo set_input_form(
                                        'switch',
                                        'stay_on_page',
                                        ucfirst(lang('stay on this page after submitting', $translations)),
                                        $data,
                                        $errors,
                                        false,
                                    );
                                @endphp
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp;
                                        @if (isset($data))
                                            {{ ucwords(lang('save', $translations)) }}
                                        @else
                                            {{ ucwords(lang('submit', $translations)) }}
                                        @endif
                                    </button>

                                    <a href="{{ route('admin.email_template') }}" class="btn btn-default"><i
                                            class="fa fa-times"></i>&nbsp;
                                        @if (isset($data))
                                            {{ ucwords(lang('close', $translations)) }}
                                        @else
                                            {{ ucwords(lang('cancel', $translations)) }}
                                        @endif
                                    </a>

                                    @if (!empty($data))
                                        <a href="javascript:void(0);" class="btn btn-primary" onclick="show_modal_test_mail_template()">
                                            <i class="fa fa-paper-plane"></i>&nbsp;
                                            {{ ucwords(lang('send test email', $translations)) }}
                                        </a>
                                    @endif

                                    @if (isset($raw_id))
                                        |&nbsp; <span class="btn btn-danger" onclick="$('#form_delete').submit()"><i class="fa fa-trash"></i></span>
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (isset($raw_id))
        <form id="form_delete" action="{{ route('admin.email_template.delete') }}" method="POST"
            onsubmit="return confirm('{!! lang('Are you sure to delete this #item?', $translations, ['#item' => 'data']) !!}');" style="display: none">
            @csrf
            <input type="hidden" name="id" value="{{ $raw_id }}">
        </form>
    @endif

    {{-- MODAL TEST SEND EMAIL TEMPLATE (DEFAULT) [BEGIN] --}}
    <div class="modal fade bs-modal-sm" id="modal_test_send_mail_template" tabindex="-1" role="dialog" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <form method="POST" action="{{ route('admin.email_template.send_email_test') }}" enctype="multipart/form-data">
                @csrf
                @php
                    if (isset($data)) {
                        // Jika $data ada, template_id diisi dengan id dari $data
                        echo '<input type="hidden" name="template_id" value="' . $data->id . '">';
                    } else {
                        // Jika $data tidak ada, template_id diisi dengan string kosong
                        echo '<input type="hidden" name="template_id" value="">';
                    }
                @endphp
                
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                        <h4 class="modal-title">
                            {{ ucwords(lang('test send #item', $translations, ['#item' => lang('email template', $translations)])) }}
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ lang('To', $translations) }}<span class="required" style="color:red">*</span></label>
                            <input type="email" class="form-control" name="to" placeholder="name@mail.com"
                                required />
                        </div>
                        <div class="form-group">
                            <label>{{ lang('Reply-To', $translations) }}</label>
                            <input type="email" class="form-control" name="reply_to" placeholder="name@mail.com" />
                        </div>
                        <div class="form-group">
                            <label>{{ lang('CC', $translations) }}</label>
                            <textarea class="form-control" name="cc" placeholder="name@mail.com"></textarea>
                            <span><i class="fa fa-info-circle"></i> {!! lang(
                                'Use a comma (,) without spaces to separate email addresses, e.g. mail1@domain.com,mail2@domain.com',
                                $translations,
                            ) !!}</span>
                        </div>
                        <div class="form-group">
                            <label>{{ lang('BCC', $translations) }}</label>
                            <textarea class="form-control" name="bcc" placeholder="name@mail.com"></textarea>
                            <span><i class="fa fa-info-circle"></i> {!! lang(
                                'Use a comma (,) without spaces to separate email addresses, e.g. mail1@domain.com,mail2@domain.com',
                                $translations,
                            ) !!}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            {{ ucwords(lang('close', $translations)) }}
                        </button>
                        <button type="submit" class="btn btn-success btn-submit">
                            {{ ucwords(lang('send', $translations)) }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- MODAL TEST SEND EMAIL TEMPLATE (DEFAULT) [END] --}}
@endsection

@section('css')
    <!-- Switchery -->
    @include('_vendors.switchery.css')
@endsection

@section('script')
    <!-- Switchery -->
    @include('_vendors.switchery.script')

    <!-- Rich Text Editor (WYSIWYG) using TinyMCE -->
    @include('_vendors.tinymce.script')
    <script>
        $(document).ready(function () {
            init_tinymce_fullpage('#email_body');
        });
    </script>

    <script>
        function show_modal_test_mail_template() {
            $('#modal_test_send_mail_template').modal();
        }
    </script>
@endsection
