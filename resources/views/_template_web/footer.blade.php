@php
    use App\Libraries\HelperWeb;

    $navigation_menu = HelperWeb::get_nav_menu();
    $company_info = HelperWeb::get_company_info();
    $social_media = HelperWeb::get_social_media();
@endphp

<!-- FOOTER -->
<style>
    .form .hs-button.primary {
        width: 132px;
        height: 30px;
    }

    .form .hs_submit {
        margin-left: 5px;
    }

    .form .input {
        align-items: center;
    }

    .form #email-e2a7e653-6512-4f3b-b816-26304a7db7a6 {
        width: 100%;
        height: 35px;
    }

    .form .hs-error-msgs,
    .hs-form-required {
        text-align: center;
    }

    .form form {
        display: inline-flex;
    }
</style>

<div class="footer"></div>
<footer>
    <div class="container">
        <div class="row_flex footer_top">
            <div class="footer_left">
                <p>{!! $company_info->description !!}</p>
                <div class="sosmed_box">
                    @if (isset($social_media))
                        @foreach ($social_media as $item)
                            <a href="{{ $item->link }}" target="_blank"><img src="{{ asset($item->logo) }}" style="display: block"></a>
                        @endforeach
                    @endif
                </div>
                <div class="latest_wrapper" id="subscribe_form">
                    <strong>Keep up to date with the latest news</strong>
                    <div class="row_clear latest_box">
                        <form action="{{ route('web.home') }}" method="POST">
                            @csrf
                            <input type="text" name="email" placeholder="Enter your email address here" required>
                            @if ($errors->has('email_address'))
                                <span class="error_msg">{{ $errors->first('email_address') }}.</span>
                            @endif
                            @if (Session::has('error_email_address'))
                                <span class="error_msg">{{ Session::get('error_email_address') }}.</span>
                            @endif
                            <button type="submit" class="def_btn">Sign Up Now</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="footer_right">
                <div class="row_flex">
                    @php
                        if (isset($navigation_menu['bottom'])) {
                            foreach ($navigation_menu['bottom'] as $menu) {
                                // set link level 1
                                switch ($menu->link_type) {
                                    case 'internal':
                                        $link_url = url('/') . $menu->link_internal;
                                        break;

                                    case 'external':
                                        $link_url = $menu->link_external;
                                        break;
                                    
                                    default:
                                        // none
                                        $link_url = '#';
                                        break;
                                }

                                // set link target level 1
                                $link_target = '_self';
                                if ($menu->link_type != 'none') {
                                    if ($menu->link_target == 'new window') {
                                        $link_target = '_blank';
                                    }
                                }

                                echo '<div class="col_two">';
                                    echo '<h5>'.$menu->name.'</h5>'; // ini bisa diberikan link
                                    echo '<ul>';
                                        // generate level 2
                                        if (isset($menu->level_2)) {
                                            foreach ($menu->level_2 as $level_2) {
                                                // set link level 2
                                                switch ($level_2->link_type) {
                                                    case 'internal':
                                                        $link_url = url('/') . $level_2->link_internal;
                                                        break;

                                                    case 'external':
                                                        $link_url = $level_2->link_external;
                                                        break;
                                                    
                                                    default:
                                                        // none
                                                        $link_url = '#';
                                                        break;
                                                }

                                                // set link target level 1
                                                $link_target = '_self';
                                                if ($level_2->link_type != 'none') {
                                                    if ($level_2->link_target == 'new window') {
                                                        $link_target = '_blank';
                                                    }
                                                }

                                                echo '<li>';
                                                    echo '<a href="'.$link_url.'" target="'.$link_target.'">'.$level_2->name.'</a>';
                                                echo '</li>';
                                            }
                                        }
                                    echo '</ul>';
                                echo '</div>';
                            }
                        }
                    @endphp
                </div>
            </div>
        </div>

        <div class="row_clear footer_bottom">
            <span><p>&copy; {{ $global_config->app_copyright_year }} {!! $global_config->app_name !!} - All right reserved.</p></span>
            @php
                if (isset($navigation_menu['footer'])) {
                    foreach ($navigation_menu['footer'] as $menu) {
                        // set link level 1
                        switch ($menu->link_type) {
                            case 'internal':
                                $link_url = url('/') . $menu->link_internal;
                                break;

                            case 'external':
                                $link_url = $menu->link_external;
                                break;
                            
                            default:
                                // none
                                $link_url = '#';
                                break;
                        }

                        // set link target level 1
                        $link_target = '_self';
                        if ($menu->link_type != 'none') {
                            if ($menu->link_target == 'new window') {
                                $link_target = '_blank';
                            }
                        }

                        echo '<a href="'.$link_url.'" target="'.$link_target.'">'.$menu->name.'</a>';
                    }
                }
            @endphp
        </div>
    </div>
</footer>

<script>
    $(function () {
        $('.footer1').hide();
    });

    var isScrolledIntoView = function (elem) {
        var $elem = $(elem);
        var $window = $(window);

        var docViewTop = $window.scrollTop();
        var docViewBottom = docViewTop + $window.height();

        var elemTop = $elem.offset().top;
        var elemBottom = elemTop + $elem.height();

        return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    }

    $(window).on('scroll', function () {
        if (isScrolledIntoView('.footer')) {
            $('.footer1').fadeOut();
        } else {
            $('.footer1').fadeIn();
        }
    });
</script>
<!-- /FOOTER -->