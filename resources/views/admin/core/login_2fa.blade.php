<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{ asset($global_config->app_favicon) }}" />

        <title>{!! $global_config->app_name !!}@if(env('ADMIN_DIR') != '') | Admin Panel @endif</title>

        <!-- Bootstrap -->
        <link href="{{ asset('vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="{{ asset('vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <!-- NProgress -->
        <link href="{{ asset('vendors/nprogress/nprogress.css') }}" rel="stylesheet">
        <!-- Animate.css -->
        <link href="{{ asset('vendors/animate.css/animate.min.css') }}" rel="stylesheet">

        <!-- Custom Theme Style -->
        <link href="{{ asset('admin/css/custom.css') }}" rel="stylesheet">

        <!-- Custom Script -->
        <script src="{{ asset('js/thehelper.js') }}?v=1.0"></script>

        @php
            $bg_img = 'images/background.jpg';
            if ($global_config->bg_images) {
                $bg_images_existing = json_decode($global_config->bg_images);
                if (isset($bg_images_existing->login)) {
                    $bg_img = $bg_images_existing->login;
                }
            }
        @endphp
        <style>
            .vlogin {
                background: #F7F7F7 url("{{ asset($bg_img) }}") no-repeat fixed center;
                background-size: cover;
            }
        </style>

        @php
            $preview_class = '';
        @endphp
        @if (isset($preview))
            @php
                $preview_class = 'container_preview';
            @endphp
            @include('_vendors.simple_preview.css')
        @endif
    
        @if (!empty($global_config->recaptcha_site_key_admin) && !empty($global_config->recaptcha_secret_key_admin))
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endif
    </head>

    <body class="login vlogin {{ $preview_class }}">
        @if (isset($preview))
            @include('_vendors.simple_preview.html')
        @endif
        <div>
            <div class="login_wrapper">
                <div class="animate form login_form">
                    {{-- display response message --}}
                    @include('_template_adm.message')

                    <section class="login_content">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">2FA Authentication</div>

                                        <div class="panel-body">
                                            <form class="form-horizontal" method="POST">
                                                @csrf

                                                <div class="form-group">
                                                    <label for="code" class="col-md-5 control-label">2FA Code</label>
                                                    <div class="col-md-6">
                                                        <input id="code" type="text" onkeyup="numbers_only(this);" class="form-control" name="code" maxlength="6" required autofocus>
                                                    </div>
                                                </div>

                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-primary">
                                                        Submit
                                                    </button>
                                                    <a href="{{ route('admin.logout') }}">
                                                        <button type="button" class="btn btn-secondary">
                                                            Cancel
                                                        </button>
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <!-- jQuery -->
        <script src="{{ asset('vendors/jquery/dist/jquery.min.js') }}"></script>
        <!-- Bootstrap -->
        <script src="{{ asset('vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                $("#submitform").on('submit',function(e) {
                    @if (!empty($global_config->recaptcha_site_key_admin) && !empty($global_config->recaptcha_secret_key_admin))
                        // check reCAPTCHA
                        var data_form = $(this).serialize();
                        var split_data = data_form.split('&');
                        var continue_step = true;
                        // check empty reCAPTCHA
                        $.each(split_data , function (index, value) {
                            var split_tmp = value.split('=');
                            if (split_tmp[0] == 'g-recaptcha-response' && split_tmp[1] == '') {
                                continue_step = false;
                                alert('{{ lang("Please check the captcha for continue", $translations) }}');
                                return false;
                            }
                        });
                        if (!continue_step) {
                            return false;
                        }
                    @endif

                    validate_form();

                    return true;
                });
            });

            function validate_form() {
                $('#btn-login').addClass('disabled');
                $('#btn-login').removeClass('btn-primary');
                $('#btn-login').addClass('btn-warning');
                $('#btn-login').html('<i class="fa fa-spin fa-spinner"></i>&nbsp; {{ ucwords(lang("loading", $translations)) }}...');

                setTimeout(function(){ window.location.reload(); }, 20000);
            }
        </script>
    </body>

</html>
