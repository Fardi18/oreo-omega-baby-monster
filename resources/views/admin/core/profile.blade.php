{{-- ADD HTML SMALL MODAL - BEGIN --}}
@extends('_template_adm.modal_small')
{{-- SMALL MODAL CONFIG --}}
@section('small_modal_id', 'modal_changepassword')
@section('small_modal_title', ucwords(lang('change #item', $translations, ['#item' => lang('password', $translations)])))
@section('small_modal_content')
    <label>{{ ucwords(lang('current #item', $translations, ['#item' => lang('password', $translations)])) }}</label>
    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-eye-slash" id="viewable-current_pass" style="cursor:pointer" onclick="viewable_password('current_pass')"></i></span>
        <input type="password" name="current_pass" id="current_pass" required="required" autocomplete="off" class="form-control col-md-7 col-xs-12">
    </div>

    <label>{{ ucwords(lang('new #item', $translations, ['#item' => lang('password', $translations)])) }}</label>
    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-eye-slash" id="viewable-new_pass" style="cursor:pointer" onclick="viewable_password('new_pass')"></i></span>
        <input type="password" name="new_pass" id="new_pass" required="required" autocomplete="off" class="form-control col-md-7 col-xs-12">
    </div>

    <label>{{ ucwords(lang('confirm #item', $translations, ['#item' => lang('password', $translations)])) }}</label>
    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-eye-slash" id="viewable-new_pass_confirmation" style="cursor:pointer" onclick="viewable_password('new_pass_confirmation')"></i></span>
        <input type="password" name="new_pass_confirmation" id="new_pass_confirmation" required="required" autocomplete="off" class="form-control col-md-7 col-xs-12">
    </div>

    <label><i class="fa fa-warning"></i>&nbsp; {{ ucwords(lang('password criteria', $translations)) }}</label>
    <ul style="padding-left: 20px !important;">
        <li>{{ lang('must be at least #min characters in length', $translations, ['#min'=>8]) }}</li>
        <li>{{ lang('must contain at least one lowercase letter', $translations) }}</li>
        <li>{{ lang('must contain at least one uppercase letter', $translations) }}</li>
        <li>{{ lang('must contain at least one digit numeric', $translations) }}</li>
        <li>{{ lang('must contain a special character (?!@#$%^&*~`_+=:;.,"><\'-)', $translations) }}</li>
    </ul>
@endsection
@section('small_modal_btn_label', ucwords(lang('submit', $translations)))
@section('small_modal_form', true)
@section('small_modal_method', 'POST')
@section('small_modal_url', route('admin.change_password'))
@section('small_modal_form_validation', 'return validate_form()')
@section('small_modal_script')
    <script>
        function validate_form() {
            var password = $('#new_pass').val();
            var password_confirmation = $('#new_pass_confirmation').val();

            if (password != password_confirmation) {
                alert("{{ lang('#item confirmation does not match', $translations, ['#item'=>ucwords(lang('password', $translations))]) }}");
                return false;
            }

            // validate password criteria
            // var regex = /^
            //     (?=.*\d)                                 // must contain at least one digit numeric
            //     (?=.*[a-z])                              // must contain at least one lowercase letter
            //     (?=.*[A-Z])                              // must contain at least one uppercase letter
            //     (?=.*[?!@#$%^&*~`_+=:;.,"><'-])          // must contain a special character (?!@#$%^&*~`_+=:;.,"><'-)
            //     [\da-zA-Z?!@#$%^&*~`_+=:;.,"><'-]{8,}    // must contain at least 8 from the mentioned characters
            // $/;
            var regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[?!@#$%^&*~`_+=:;.,"><'-])[\da-zA-Z?!@#$%^&*~`_+=:;.,"><'-]{8,}$/;

            if (regex.test(password)) {
                // continue
            } else {
                alert("{{ lang('#item format is invalid', $translations, ['#item'=>ucwords(lang('password', $translations))]) }}");
                return false;
            }
            
            $('.btn-submit').addClass('disabled');
            $('.btn-submit').html('<i class="fa fa-spin fa-spinner"></i>&nbsp; {{ ucwords(lang("loading", $translations)) }}');
            return true;
        }
    </script>
@endsection
{{-- ADD HTML SMALL MODAL - END --}}

@extends('_template_adm.master')

@php
    // Libraries
    use App\Libraries\Helper;

    $pagetitle = ucwords(lang('my profile', $translations));
    
    $last_login_label = ucwords(lang('unknown', $translations));
    if (isset($last_login)) {
        $last_login_label = Helper::time_ago(strtotime($last_login->created_at), lang('ago', $translations), Helper::get_periods($translations)) . '<br>' . Helper::locale_timestamp($last_login->created_at);
    }

    $user_session = Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'));
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

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_content">
                        <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
                            <div class="profile_img">
                                <div id="crop-avatar">
                                    <!-- Current avatar -->
                                    <img class="img-responsive avatar-view" src="{{ Helper::get_avatar() }}" alt="Avatar" title="Change the avatar">
                                </div>
                            </div>

                            <h3>{{ $data->fullname }}</h3>

                            <ul class="list-unstyled user_data">
                                <li>
                                    <i class="fa fa-user user-profile-icon"></i>&nbsp; {{ $data->username }}
                                </li>

                                @if ($data->phone)
                                    <li class="m-top-xs">
                                        <i class="fa fa-phone user-profile-icon"></i>&nbsp;
                                        <a href="tel:{{ $data->phone }}" target="_blank">{{ $data->phone }}</a>
                                    </li>
                                @endif

                                <li class="m-top-xs">
                                    <i class="fa fa-envelope user-profile-icon"></i>&nbsp;
                                    <a href="mailto:{{ $data->email }}" target="_blank">{{ $data->email }}</a>
                                </li>

                                <li>
                                    <b>{{ ucfirst(lang('last login', $translations)) }}</b>: {!! $last_login_label !!}
                                </li>
                            </ul>

                            <a class="btn btn-success" onclick="$('#profile-tab').click()"><i class="fa fa-edit m-right-xs"></i>&nbsp; {{ ucwords(lang('edit #item', $translations, ['#item' => lang('profile', $translations)])) }}</a>
                        </div>

                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="" role="tabpanel" data-example-id="togglable-tabs">
                                <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">{{ ucwords(lang('recent activity', $translations)) }}</a>
                                    </li>
                                    <li role="presentation" class="">
                                        <a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">{{ ucwords(lang('profile', $translations)) }}</a>
                                    </li>
                                </ul>
                                <div id="myTabContent" class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                                        <!-- start recent activity -->
                                        <ul class="messages">
                                            @if (isset($logs[0]))
                                                @foreach ($logs as $item)
                                                    <li>
                                                        <img src="{{ Helper::get_avatar() }}" class="avatar" alt="Avatar">
                                                        <div class="message_wrapper">
                                                            {{-- <h4 class="heading">Desmond Davison</h4> --}}
                                                            @php
                                                                $arr_log_label = [];
                                                                $arr_log_label[] = $item->action;
                                                                if ($item->module_name) {
                                                                    $arr_log_label[] = $item->module_name;
                                                                }
                                                                if ($item->note) {
                                                                    $arr_log_label[] = $item->note;
                                                                }
                                                            @endphp
                                                            <blockquote class="message">{!! implode(' ', $arr_log_label) !!}</blockquote>
                                                            <br />
                                                            <p class="url">
                                                                <span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
                                                                {{-- Sat, 20 Feb 2021 20:35 (Asia/Jakarta) --}}
                                                                {{ Helper::locale_timestamp($item->created_at) }}
                                                            </p>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                        <!-- end recent activity -->

                                    </div>

                                    <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
                                        <form class="form-horizontal form-label-left" action="{{ route('admin.profile') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                    
                                            @php
                                                // set_input_form($type, $input_name, $label_name, $data, $errors, $required = false, $config = null)
                                                $config = new \stdClass();
                                                $config->attributes = 'readonly';
                                                $config->placeholder = lang('must be unique', $translations);
                                                echo set_input_form('word', 'username', ucwords(lang('username', $translations)), $data, $errors, false, $config);

                                                $config = new \stdClass();
                                                $config->delete = true;
                                                $config->popup = true;
                                                $config->info = '<i class="fa fa-info-circle"></i>&nbsp; '.lang('Only support #allowed_ext, MAX #size', $translations, ['#allowed_ext'=>'jpg/png/gif', '#size'=>'2MB']);
                                                echo set_input_form('image', 'avatar_with_path', ucwords(lang('avatar', $translations)), $data, $errors, false, $config);

                                                $config = new \stdClass();
                                                $config->attributes = 'autocomplete="off"';
                                                echo set_input_form('text', 'firstname', ucwords(lang('firstname', $translations)), $data, $errors, true, $config);

                                                $config = new \stdClass();
                                                $config->attributes = 'autocomplete="off"';
                                                echo set_input_form('text', 'lastname', ucwords(lang('lastname', $translations)), $data, $errors, true, $config);
                    
                                                $config = new \stdClass();
                                                $config->attributes = 'autocomplete="off"';
                                                $config->placeholder = 'username@domain.com';
                                                echo set_input_form('email', 'email', ucwords(lang('email', $translations)), $data, $errors, true, $config);
                                                
                                                $config = new \stdClass();
                                                $config->attributes = 'autocomplete="off"';
                                                $config->placeholder = '8123456789';
                                                $config->input_addon = env('COUNTRY_CODE');
                                                echo set_input_form('number_only', 'phone', ucwords(lang('phone', $translations)), $data, $errors, false, $config);
                                            @endphp
                    
                                            <div class="ln_solid"></div>

                                            <div class="form-group">
                                                <div class="col-md-12 text-center">
                                                    <span class="btn btn-primary btn-round" data-toggle="modal" data-target="#modal_changepassword">
                                                        <i class="fa fa-unlock-alt"></i>&nbsp; {{ ucwords(lang('change #item', $translations, ['#item' => lang('password', $translations)])) }}
                                                    </span>

                                                    @if (env('2FA_ENABLED', false))
                                                        <span class="btn btn-info btn-round" data-toggle="modal" data-target="#modal_2fa">
                                                            <i class="fa fa-shield"></i>&nbsp; {{ ucwords(lang('2FA setup', $translations)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>                                            
                                            
                                            <div class="ln_solid"></div>
                    
                                            <div class="form-group">
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp; {{ ucwords(lang('save', $translations)) }}</button>
                                                    {{-- <a href="{{ route('admin.logout.all') }}" class="btn btn-danger" onclick="return confirm('{{ lang('Are you sure to logout your account from all sessions?', $translations) }}')">
                                                        {{ ucwords(lang('logout all sessions', $translations)) }}&nbsp; <i class="fa fa-sign-out"></i>
                                                    </a> --}}
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (env('2FA_ENABLED', false))
        <div class="modal fade bs-modal-md" tabindex="-1" role="dialog" aria-hidden="true" id="modal_2fa">
            <div class="modal-dialog modal-md">
                <form method="post" action="{{ route('admin.setup_2fa') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">{{ ucwords(lang('2FA Setup', $translations)) }}</h4>
                        </div>
                        <div class="modal-body">
                            <h4>{{ (lang('Protect your account with 2FA (Two-Factor Authentication)', $translations)) }}</h4>
                            <p>{{ (lang('Prevent hackers from accessing your account with an additional layer of security. Enable now.', $translations)) }}</p>

                            <h4>{{ (lang('Setup authenticator app', $translations)) }}</h4>
                            <p>{{ (lang('Authenticator app generates one-time passwords that are used as second factor to verify your identity when prompted during sign-in.', $translations)) }}</p>
                            <p>{{ (lang('Download Google Authenticator app:', $translations)) }} <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank"><u>Android <i class="fa fa-external-link"></i></u></a> / <a href="https://apps.apple.com/app/google-authenticator/id388497605" target="_blank"><u>iPhone & iPad <i class="fa fa-external-link"></i></u></a></p>
                            <hr>

                            @if (!$data->google2fa_enabled)
                                <h5>{{ (lang('Scan the QR code', $translations)) }}</h5>
                                <p>{{ (lang('Use Google authenticator app to scan.', $translations)) }}</p>
                                <div class="text-center">
                                    {!! $qr_image !!}
                                </div>
                                <p>{{ (lang('Unable to scan? You can use the setup key to manually configure your authenticator app.', $translations)) }}</p>
                                <div class="input-group">
                                    <input type="text" id="twofa_setup_key" readonly value="{{ $data->google2fa_secret }}" class="form-control col-md-7 col-xs-12">
                                    <span class="input-group-addon"><i class="fa fa-copy" title="copy" style="cursor:pointer" onclick="alert('copied')"></i></span>
                                </div>
                                <hr>
                                <label>{{ (lang('Verify the code from the app', $translations, ['#item' => lang('password', $translations)])) }}</label>
                                <input type="text" name="code" required="required" autocomplete="off" placeholder="XXXXXX" class="form-control">
                            @else
                                <h5>{{ (lang('Disable 2FA (Two-Factor Authentication)', $translations)) }}</h5>
                                <div class="clearfix"></div>
                                <div class="alert alert-warning alert-dismissible fade in" role="alert">
                                    <h4><i class="icon fa fa-warning"></i> {{ (lang('Are you sure to disable 2FA (Two-Factor Authentication)?', $translations)) }}</h4>
                                    {{ (lang('2FA (Two-Factor Authentication) prevents hackers from accessing your account with an additional layer of security.', $translations)) }}
                                </div>
                                <hr>
                                <label>{{ (lang('To confirm, input the code from the app', $translations, ['#item' => lang('password', $translations)])) }}</label>
                                <input type="text" name="code" required="required" autocomplete="off" placeholder="XXXXXX" class="form-control">
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ ucwords(lang('close', $translations)) }}</button>
                            <button type="submit" class="btn btn-primary btn-submit">
                                {{ ucwords(lang('save', $translations)) }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('css')
    <!-- PhotoSwipe -->
    @include('_vendors.photoswipe.css')
@endsection

@section('script')
    <!-- PhotoSwipe -->
    @include('_vendors.photoswipe.script')
@endsection