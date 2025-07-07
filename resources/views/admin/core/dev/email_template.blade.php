@extends('_template_adm.master')

@php
    $pagetitle = ucwords(lang('email template', $translations));
    $link = route('dev.email_template');
    if (!isset($data)) {
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
                <div class="x_panel">
                    <div class="x_title">
                        <h2>{{ ucwords(lang('form details', $translations)) }}</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br />
                        {{-- <div class="alert alert-info">
                            <h4><i class="fa fa-info-circle"></i> Info</h4>
                            This tool for encrypt string using <a href="https://github.com/defuse/php-encryption" target="_blank" style="color:white !important; text-decoration: underline !important;">defuse/php-encryption &nbsp;<i class="fa fa-external-link"></i></a>
                        </div> --}}
                        <form id="form_data" class="form-horizontal form-label-left" action="{{ $link }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            @php
                                // set_input_form($type, $input_name, $label_name, $data, $errors, $required = false, $config = null)
                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                echo set_input_form('text', 'email_test_subject', ucwords(lang('email subject', $translations)), $data, $errors, true, $config);
                                
                                $config = new \stdClass();
                                $config->autosize = true;
                                if (empty($data->email_test_template)) {
                                    $config->value = '<!DOCTYPE html>';
                                    $config->value .= '<html>';
                                        $config->value .= '<head>';
                                            $config->value .= '<title>Email Subject</title>';
                                        $config->value .= '</head>';
                                        $config->value .= '<body style="max-width: 600px;">';
                                            $config->value .= '<table style="background: #fafafa;width: 100%;max-width: 600px;margin:0 auto;font-family: Arial, Helvetica, sans-serif;border-collapse: collapse;">';
                                                $config->value .= '<thead>';
                                                    $config->value .= '<tr>';
                                                        $config->value .= '<th style="padding: 10px;text-align: left;vertical-align: bottom;">';
                                                            $config->value .= '<img style="display: block;width: 100px;" src="https://legion.lenovo.com/revamp/images/logo_black.png">';
                                                        $config->value .= '</th>';
                                                    $config->value .= '</tr>';
                                                $config->value .= '</thead>';
                                                $config->value .= '<tbody>';
                                                    $config->value .= '<tr>';
                                                        $config->value .= '<td colspan="2">';
                                                            $config->value .= '<img style="width: 500px; display: block; margin-bottom: 20px;" src="https://legion.isysedge.com/images/go_hero.png" height="197" />';
                                                        $config->value .= '</td>';
                                                    $config->value .= '</tr>';
                                                    $config->value .= '<tr>';
                                                        $config->value .= '<td colspan="2" style="padding:0px 10px 0px;">';
                                                            $config->value .= '<p>Dear {User Name},</p>';
                                                        $config->value .= '</td>';
                                                    $config->value .= '</tr>';
                                                    $config->value .= '<tr>';
                                                        $config->value .= '<td colspan="2" style="padding:0px 10px 0px;">';
                                                            $config->value .= '<p>Thank you for joining the Legion Go Software Beta Testing Program.</p>';
                                                            $config->value .= '<p>The Legion Go Software Beta Testing Program is a non-paid program that provide access to a limited Legion Go enthusiast group to latest software files.</p>';
                                                            $config->value .= '<p>Only selected users can join this program, and you are selected.<br />Congratulations! 🥳</p>';
                                                        $config->value .= '</td>';
                                                    $config->value .= '</tr>';
                                                    $config->value .= '<tr>';
                                                        $config->value .= '<td colspan="2" style="padding:0px 10px 0px;">';
                                                            $config->value .= '<p>We need your feedback for testing below:</p>';
                                                        $config->value .= '</td>';
                                                    $config->value .= '</tr>';
                                                    $config->value .= '<tr>';
                                                        $config->value .= '<td colspan="2" style="text-align: left;padding:0px 10px 0px;">';
                                                            $config->value .= '<p>File Name: {File Name}</p>';
                                                            $config->value .= '<p>Download Link: {File URL}</p>';
                                                            $config->value .= '<p>Description: {File Description}</p>';
                                                        $config->value .= '</td>';
                                                    $config->value .= '</tr>';
                                                    $config->value .= '<tr>';
                                                        $config->value .= '<td colspan="2" style="padding:0px 10px 0px;">';
                                                            $config->value .= '<p>We appreciate your interest in our product Legion GO, and we are waiting your feedback for this update.</p>';
                                                        $config->value .= '</td>';
                                                    $config->value .= '</tr>';
                                                    $config->value .= '<tr>';
                                                        $config->value .= '<td colspan="2" style="padding:0px 10px 0px;">';
                                                            $config->value .= '<p>If there are any questions or issues about this update, please submit a new feedback in <a href="https://legion.isysedge.com/beta-program">Lenovo Legion - Beta Program</a>.</p>';
                                                        $config->value .= '</td>';
                                                    $config->value .= '</tr>';
                                                    $config->value .= '<tr>';
                                                        $config->value .= '<td colspan="2" style="padding:0px 10px 0px;">';
                                                            $config->value .= '<p>Thank you for choosing to participate in our beta program, and we look forward to give you the best experience with our product.';
                                                            $config->value .= '</p>';
                                                        $config->value .= '</td>';
                                                    $config->value .= '</tr>';
                                                $config->value .= '</tbody>';
                                                $config->value .= '<tfoot>';
                                                    $config->value .= '<tr>';
                                                        $config->value .= '<th colspan="2" style="text-align: left;font-weight: normal;font-size: 14px;padding:10px;">';
                                                            $config->value .= '<p>Best regards,</p>';
                                                            $config->value .= '<br>';
                                                            $config->value .= '<p>Lenovo</p>';
                                                            $config->value .= '<em>*This message was sent from an unmonitored email address. Please do not reply to this message.</em></th>';
                                                        $config->value .= '</th>';
                                                    $config->value .= '</tr>';
                                                $config->value .= '</tfoot>';
                                            $config->value .= '</table>';
                                        $config->value .= '</body>';
                                    $config->value .= '</html>';
                                }
                                echo set_input_form('textarea', 'email_test_template', ucwords(lang('email template', $translations)), $data, $errors, true, $config);
                            @endphp
                            
                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save"></i>&nbsp; {{ ucwords(lang('save', $translations)) }}
                                    </button>
                                    @if (!empty($data->email_test_template))
                                        {{-- <a href="{{ route('admin.promotion.redemption.preview_email_template', ['id' => $raw_id, 'email_template' => 'email_submission']) }}" target="_blank" class="btn btn-warning">
                                            <i class="fa fa-eye"></i>&nbsp; {{ ucwords(lang('preview', $translations)) }}
                                        </a> --}}
                                        <a href="javascript:void(0);" class="btn btn-primary" onclick="show_modal_test_mail_template()">
                                            <i class="fa fa-paper-plane"></i>&nbsp; {{ ucwords(lang('test send email', $translations)) }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TEST SEND EMAIL TEMPLATE (DEFAULT) [BEGIN] --}}
    <div class="modal fade bs-modal-sm" id="modal_test_send_mail_template" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <form method="POST" action="{{ route('dev.email_template') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="action" value="send">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">{{ ucwords(lang('test send #item', $translations, ['#item' => lang('email template', $translations)])) }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ (lang('to', $translations)) }}<span class="required" style="color:red">*</span></label>
                            <input type="email" class="form-control" name="email_to" placeholder="name@mail.com" required />
                        </div>
                        <div class="form-group">
                            <label>{{ (lang('reply-to', $translations)) }}</label>
                            <input type="email" class="form-control" name="email_reply_to" placeholder="name@mail.com" />
                        </div>
                        <div class="form-group">
                            <label>{{ (lang('cc', $translations)) }}</label>
                            <textarea class="form-control" name="email_cc" placeholder="name@mail.com"></textarea>
                            <span><i class="fa fa-info-circle"></i> {!! lang('Use a comma (,) without spaces to separate email addresses, e.g. mail1@domain.com,mail2@domain.com', $translations) !!}</span>
                        </div>
                        <div class="form-group">
                            <label>{{ (lang('bcc', $translations)) }}</label>
                            <textarea class="form-control" name="email_bcc" placeholder="name@mail.com"></textarea>
                            <span><i class="fa fa-info-circle"></i> {!! lang('Use a comma (,) without spaces to separate email addresses, e.g. mail1@domain.com,mail2@domain.com', $translations) !!}</span>
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
    
@endsection

@section('script')
    <!-- Textarea Autosize -->
    @include('_vendors.autosize.script')

    <!-- Rich Text Editor (WYSIWYG) using TinyMCE -->
    @include('_vendors.tinymce.script')
    <script>
        $(document).ready(function () {
            init_tinymce_fullpage('#email_test_template');
        });
    </script>

    <script>
        function show_modal_test_mail_template() {
            $('#modal_test_send_mail_template').modal();
        }
    </script>
@endsection