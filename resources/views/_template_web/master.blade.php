@php
    // Libraries
    use App\Libraries\Helper;
@endphp

<!DOCTYPE html>
<!--[if IE 8]>			<html class="ie ie8"> <![endif]-->
<!--[if IE 9]>			<html class="ie ie9"> <![endif]-->
<!--[if gt IE 9]><!-->
<html>
<!--<![endif]-->

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
        <meta name="HandheldFriendly" content="true" />
        <link rel="icon" href="{{ asset($global_config->app_favicon) }}" />
        <title>@if(View::hasSection('title'))@yield('title') | {!! $global_config->app_name !!}@else {!! $global_config->meta_title !!} @endif</title>

        <meta property="og:type" content="{!! $global_config->og_type !!}" />
        <meta property="og:site_name" content="{!! $global_config->og_site_name !!}" />
        <meta property="og:url" content="{{ Helper::get_url() }}" />
        
        @if(View::hasSection('open_graph'))
            @yield('open_graph')
        @else
            {{-- DEFAULT OPEN GRAPH --}}
            @if (isset($global_config->og_type))
                <meta name="description" content="{!! $global_config->meta_description !!}">
                <meta name="keywords" content="{!! str_replace(',', ', ', $global_config->meta_keywords) !!}">
                <meta name="author" content="{!! $global_config->meta_author !!}">
                
                <meta property="og:title" content="@if(View::hasSection('title'))@yield('title')@else{!! $global_config->og_title !!}@endif" />
                <meta property="og:image" content="{{ asset($global_config->og_image) }}" />
                <meta property="og:description" content="{!! $global_config->og_description !!}" />
                
                @if ($global_config->fb_app_id)
                    <meta property="fb:app_id" content="{!! $global_config->fb_app_id !!}" />
                @endif

                <meta property="twitter:card" content="{!! $global_config->twitter_card !!}" />
                @if ($global_config->twitter_site)
                    <meta property="twitter:site" content="{!! $global_config->twitter_site !!}" />
                @endif
                @if ($global_config->twitter_site_id)
                    <meta property="twitter:site:id" content="{!! $global_config->twitter_site_id !!}" />
                @endif
                @if ($global_config->twitter_creator)
                    <meta property="twitter:creator" content="{!! $global_config->twitter_creator !!}" />
                @endif
                @if ($global_config->twitter_creator_id)
                    <meta property="twitter:creator:id" content="{!! $global_config->twitter_creator_id !!}" />
                @endif
            @endif
        @endif

        {!! $global_config->header_script !!}

        <!-- THEME CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('web/css/jquery.dmenu.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('web/fonts/stylesheet.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('web/css/slick.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('web/css/slick-theme.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('web/css/select2.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('web/css/main.css') }}?v=1.6">

        {{-- Custom CSS --}}
        @yield('style')
        
        <!-- Custom Script -->
        <script src="{{ asset('js/thehelper.js') }}?v=1.0"></script>
        {{-- <script src="{{ asset('admin/js/vcustom.js?v=1') }}"></script> --}}
        <script src="{{ asset('web/js/jquery.js') }}"></script>
        <script src="{{ asset('web/js/jquery.dmenu.js') }}"></script>
        <script src="{{ asset('web/js/slick.js') }}"></script>
        <script src="{{ asset('web/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('web/js/main.js') }}?v=1.2"></script>

        @yield('header-script')
    </head>

    <body>
        {!! $global_config->body_script !!}

        @yield('body-script')
        <div class="wrapper">
            {{-- HEADER --}}
            {{-- @include('_template_web.header') --}}

            {{-- CONTENT --}}
            @yield('content')

            {{-- FOOTER --}}
            {{-- @include('_template_web.footer') --}}
        </div>
        
        <script>
            $(function () {
                $(window).resize();
            });

            $('#menu').dmenu({
                menu: {
                    logo: true,
                    align: 'right'
                },
                item: {
                    bg: true,
                    border: false,
                    subindicator: true,

                    fit: [{
                            items: null,
                            fitter: 'icon-hide',
                            order: 'all'
                        },
                        {
                            items: null,
                            fitter: 'icon-only',
                            order: 'all'
                        },
                        {
                            items: ':not(.dm-item_align-right)',
                            fitter: 'submenu',
                            order: 'rtl'
                        },
                        {
                            items: ':not(.dm-item_align-right)',
                            fitter: 'hide',
                            order: 'rtl'
                        }
                    ]
                },
                submenu: {
                    arrow: false,
                    border: false,
                    shadow: true
                },
                subitem: {
                    bg: true,
                    border: false
                }

            });

            $(document).ready(function () {
                $('.dm-item_submenu-mega').each(function () {
                    if ($(this).find('ul li.submainli').length > 3) {
                        $(this).find('.submain').addClass('active').css('width', 960);
                        $(this).find('.submainli').css('width', '33.333%');
                    }
                })
                $('nav ul li.submenuclick').click(function () {
                    if ($(this).find('ul.submainhover').css('display') == "none") {
                        $('ul.submainhover').removeClass('selected');
                        $(this).find('ul.submainhover').addClass('selected');
                    } else {
                        $(this).find('ul.submainhover').removeClass('selected');
                    }
                })
                var getheightheader = $('header').height();
                if ($('.notif_box').length > 0) {
                    $('section').delay(100).css('padding-top', getheightheader);
                }
            });

            $(window).bind('load, resize', function () {
                var getheightheader2 = $('header').height();
                if ($('.notif_box').length > 0) {
                    $('section').delay(100).css('padding-top', getheightheader2);
                }
            });
        </script>

        @yield('footer-script')

        {!! $global_config->footer_script !!}
    </body>

</html>