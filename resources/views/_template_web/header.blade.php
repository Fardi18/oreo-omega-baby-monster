@php
    use App\Libraries\Helper;
    use App\Libraries\HelperWeb;

    $navigation_menu = HelperWeb::get_nav_menu();
@endphp

<!-- HEADER -->
<header>
    <a href="#" class="notif_box">
        <p style="text-align: center;">
            Hello World!
        </p>
    </a>
    <div class="header_box">
        <h1><a href="{{ route('web.home') }}" id="logo">{!! $global_config->app_name !!}</a></h1>
        <nav id="menu">
            <ul>
                @php
                    if (isset($navigation_menu['top'])) {
                        foreach ($navigation_menu['top'] as $menu) {
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

                            if (isset($menu->level_2)) {
                                // punya level 2

                                // tidak punya level 3, hanya level 2
                                $has_level_3 = false;

                                // cek apakah level 1 ini punya level 3
                                foreach ($menu->level_2 as $level_2) {
                                    if (isset($level_2->level_3)) {
                                        // punya level 3
                                        $has_level_3 = true;
                                    }
                                }

                                if ($has_level_3) {
                                    // punya level 3
                                    echo '<li class="SubmenuMega submenuclick">';
                                        echo '<a href="'.$link_url.'" class="nav_btn" target="'.$link_target.'">'.$menu->name.'</a>';
                                        echo '<span class="dd_trigger"></span>';
                                        echo '<ul class="submainhover submain">';
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

                                                // set link target level 2
                                                $link_target = '_self';
                                                if ($level_2->link_type != 'none') {
                                                    if ($level_2->link_target == 'new window') {
                                                        $link_target = '_blank';
                                                    }
                                                }

                                                echo '<li class="submainli">';
                                                    echo '<a href="'.$link_url.'" class="nav_btn" target="'.$link_target.'">'.$level_2->name.'</a>';
                                                    
                                                    // jika level 2 punya level 3, maka di-generate
                                                    if (isset($level_2->level_3)) {
                                                        echo '<ul>';
                                                            foreach ($level_2->level_3 as $level_3) {
                                                                // set link level 3
                                                                switch ($level_3->link_type) {
                                                                    case 'internal':
                                                                        $link_url = url('/') . $level_3->link_internal;
                                                                        break;

                                                                    case 'external':
                                                                        $link_url = $level_3->link_external;
                                                                        break;
                                                                    
                                                                    default:
                                                                        // none
                                                                        $link_url = '#';
                                                                        break;
                                                                }

                                                                // set link target level 3
                                                                $link_target = '_self';
                                                                if ($level_3->link_type != 'none') {
                                                                    if ($level_3->link_target == 'new window') {
                                                                        $link_target = '_blank';
                                                                    }
                                                                }

                                                                echo '<li>';
                                                                    echo '<a href="'.$link_url.'" class="nav_btn" target="'.$link_target.'">'.$level_3->name.'</a>';
                                                                echo '</li>'; # /.submainli
                                                            }
                                                        echo '</ul>';
                                                    }
                                                echo '</li>'; # /.submainli
                                            }
                                        echo '</ul>'; # /.submainhover
                                    echo '</li>'; # /.submenuclick
                                } else {
                                    // tidak punya level 3, hanya level 2
                                    echo '<li class="submenuclick">';
                                        echo '<a href="'.$link_url.'" class="nav_btn" target="'.$link_target.'">'.$menu->name.'</a>';
                                        echo '<span class="dd_trigger"></span>';
                                        echo '<ul class="submainhover">';
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

                                                // set link target level 2
                                                $link_target = '_self';
                                                if ($level_2->link_type != 'none') {
                                                    if ($level_2->link_target == 'new window') {
                                                        $link_target = '_blank';
                                                    }
                                                }

                                                echo '<li>';
                                                    echo '<a href="'.$link_url.'" class="nav_btn" target="'.$link_target.'">'.$level_2->name.'</a>';
                                                echo '</li>'; # /.submainli
                                            }
                                        echo '</ul>'; # /.submainhover
                                    echo '</li>'; # /.submenuclick
                                }
                            } else {
                                // tidak punya level 2, hanya level 1
                                echo '<li class="submenuclick">';
                                    echo '<a href="'.$link_url.'" class="nav_btn" target="'.$link_target.'">'.$menu->name.'</a>';
                                echo '</li>'; # /.submenuclick
                            }
                        }
                    }
                @endphp
            </ul>
        </nav>
        <div class="menu_mobile"></div>
    </div>
</header>
<!-- /HEADER -->

<style>
    header #logo {
        background: url("{{ asset($global_config->app_logo) }}") no-repeat top center/contain;
    }
</style>