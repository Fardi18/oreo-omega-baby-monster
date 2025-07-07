@php
    // Libraries
    use App\Libraries\Helper;

    $badge_new = '<span class="label label-success pull-right">NEW</span>';
@endphp

<!-- sidebar menu -->
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h3>{{ ucwords(lang('main menu', $translations)) }}</h3>
        <ul class="nav side-menu">
            <li>
                <a href="{{ route('admin.home') }}">
                    <i class="fa fa-dashboard"></i> {{ ucwords(lang('dashboard', $translations)) }}
                </a>
            </li>

            {{-- WEBSITE --}}
            @php
                $library_menus = [];

                $positions = ['home', 'about'];
                foreach ($positions as $position) {
                    $library_menus[ucwords($position) . ' Banner'] = [
                        'module' => 'Banner',
                        'rule' => 'View List',
                        'prefix' => 'banner/'.$position,
                        'route_name' => 'admin.banner',
                        'route_params' => [$position],
                        'menu_label' => ucwords(lang('#item banner', $translations, ['#item' => ucwords(lang($position, $translations))])),
                    ];
                }

                $positions = ['top', 'bottom'];
                foreach ($positions as $position) {
                    $library_menus[ucwords($position) . ' Navigation Menu'] = [
                        'module' => 'Navigation Menu',
                        'rule' => 'View List',
                        'prefix' => 'nav-menu/'.$position,
                        'route_name' => 'admin.nav_menu',
                        'route_params' => [$position],
                        'menu_label' => ucwords(lang('#item nav menu', $translations, ['#item' => ucwords(lang($position, $translations))])),
                    ];
                }

                $library_menus['Social Media'] = [
                    'module' => 'Social Media',
                    'rule' => 'View List',
                    'prefix' => 'social-media',
                    'route_name' => 'admin.social_media',
                    'route_params' => [],
                    'menu_label' => ucwords(lang('social media', $translations)),
                ];

                $library_menus['Page'] = [
                    'module' => 'Page',
                    'rule' => 'View List',
                    'prefix' => 'page',
                    'route_name' => 'admin.page',
                    'route_params' => [],
                    'menu_label' => ucwords(lang('page', $translations)),
                ];

                $library_menus['FAQ'] = [
                    'module' => 'FAQ',
                    'rule' => 'View List',
                    'prefix' => 'faq',
                    'route_name' => 'admin.faq',
                    'route_params' => [],
                    'menu_label' => (lang('FAQ', $translations)),
                ];

                $library_menus['Form'] = [
                    'module' => 'Form',
                    'rule' => 'View List',
                    'prefix' => 'form',
                    'route_name' => 'admin.form',
                    'route_params' => [],
                    'menu_label' => ucwords(lang('form', $translations)),
                ];

                $library_authorized = false;
                foreach ($library_menus as $config) {
                    if (Helper::authorizing($config['module'], $config['rule'])['status'] == 'true') {
                        $library_authorized = true;
                        break;
                    }
                }
            @endphp
            @if ($library_authorized)
                @php
                    $parent_menu_active = '';
                    $style_child_menu = '';
                    foreach ($library_menus as $config) {
                        if(Helper::is_menu_active('/'.$config['prefix'])){
                            $parent_menu_active = 'current-page active';
                            $style_child_menu = 'display: block;';
                            break;
                        }
                    }
                @endphp
                <li class="{{ $parent_menu_active }}">
                    <a><i class="fa fa-globe"></i> {{ strtoupper(lang('website', $translations)) }} <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu" style="{{ $style_child_menu }}">
                        @foreach ($library_menus as $config)
                            @if (Helper::authorizing($config['module'], $config['rule'])['status'] == 'true')
                                @php
                                    $menu_active = '';
                                    if(Helper::is_menu_active('/'.$config['prefix'])){
                                        $menu_active = 'current-page';
                                    }
                                @endphp
                                <li class="{{ $menu_active }}">
                                    <a href="{{ route($config['route_name'], $config['route_params']) }}">
                                        {{ $config['menu_label'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                  </li>
            @endif

            {{-- ACCOUNTS --}}
            @php
                $library_menus = [
                    'Administrator' => [
                        'module' => 'Administrator',
                        'rule' => 'View List',
                        'prefix' => 'administrator',
                        'route_name' => 'admin.user_admin',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('administrators', $translations)),
                    ],
                    'Admin Group' => [
                        'module' => 'Admin Group',
                        'rule' => 'View List',
                        'prefix' => 'group',
                        'route_name' => 'admin.group',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('admin groups', $translations)),
                    ],
                ];

                $library_authorized = false;
                foreach ($library_menus as $config) {
                    if (Helper::authorizing($config['module'], $config['rule'])['status'] == 'true') {
                        $library_authorized = true;
                        break;
                    }
                }
            @endphp
            @if ($library_authorized)
                @php
                    $parent_menu_active = '';
                    $style_child_menu = '';
                    foreach ($library_menus as $config) {
                        if(Helper::is_menu_active('/'.$config['prefix'])){
                            $parent_menu_active = 'current-page active';
                            $style_child_menu = 'display: block;';
                            break;
                        }
                    }
                @endphp
                <li class="{{ $parent_menu_active }}">
                    <a><i class="fa fa-users"></i> {{ strtoupper(lang('accounts', $translations)) }} <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu" style="{{ $style_child_menu }}">
                        @foreach ($library_menus as $config)
                            @if (Helper::authorizing($config['module'], $config['rule'])['status'] == 'true')
                                @php
                                    $menu_active = '';
                                    if(Helper::is_menu_active('/'.$config['prefix'])){
                                        $menu_active = 'current-page';
                                    }
                                @endphp
                                <li class="{{ $menu_active }}">
                                    <a href="{{ route($config['route_name'], $config['route_params']) }}">
                                        {{ $config['menu_label'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                  </li>
            @endif

            {{-- SETTINGS --}}
            @php
                $library_menus = [
                    'Config' => [
                        'module' => 'Config',
                        'rule' => 'View',
                        'prefix' => 'config',
                        'route_name' => 'admin.config',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('configuration', $translations)),
                    ],
                    'Email Template' => [
                        'module' => 'Email Template',
                        'rule' => 'View List',
                        'prefix' => 'email-template',
                        'route_name' => 'admin.email_template',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('email templates', $translations)),
                    ],
                    'Office' => [
                        'module' => 'Office',
                        'rule' => 'View List',
                        'prefix' => 'office',
                        'route_name' => 'admin.office',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('offices', $translations)),
                    ],
                    'Module' => [
                        'module' => 'Module',
                        'rule' => 'View List',
                        'prefix' => 'module',
                        'route_name' => 'admin.module',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('modules', $translations)),
                    ],
                    'Rules' => [
                        'module' => 'Rules',
                        'rule' => 'View List',
                        'prefix' => 'rules',
                        'route_name' => 'admin.module_rule',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('rules', $translations)),
                    ],
                    'Country' => [
                        'module' => 'Country',
                        'rule' => 'View List',
                        'prefix' => 'country',
                        'route_name' => 'admin.country',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('country', $translations)),
                    ],
                    'Phrase' => [
                        'module' => 'Phrase',
                        'rule' => 'View List',
                        'prefix' => 'phrase',
                        'route_name' => 'admin.phrase',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('phrase', $translations)),
                    ],
                    'System Logs' => [
                        'module' => 'System Logs',
                        'rule' => 'View List',
                        'prefix' => 'system-logs',
                        'route_name' => 'admin.system_logs',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('system logs', $translations)),
                    ],
                    'Error Logs' => [
                        'module' => 'Error Logs',
                        'rule' => 'View List',
                        'prefix' => 'error-logs',
                        'route_name' => 'admin.error_logs',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('error logs', $translations)),
                    ],
                ];

                if ($global_config->secure_login) {
                    $library_menus['Blocked IP'] = [
                        'module' => 'Blocked IP',
                        'rule' => 'View List',
                        'prefix' => 'blocked-ip',
                        'route_name' => 'admin.blocked_ip',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('blocked IP', $translations)),
                    ];
                }

                $library_authorized = false;
                foreach ($library_menus as $config) {
                    if (Helper::authorizing($config['module'], $config['rule'])['status'] == 'true') {
                        $library_authorized = true;
                        break;
                    }
                }
            @endphp
            @if ($library_authorized)
                @php
                    $parent_menu_active = '';
                    $style_child_menu = '';
                    foreach ($library_menus as $config) {
                        if(Helper::is_menu_active('/'.$config['prefix'])){
                            $parent_menu_active = 'current-page active';
                            $style_child_menu = 'display: block;';
                            break;
                        }
                    }
                @endphp
                <li class="{{ $parent_menu_active }}">
                    <a><i class="fa fa-gears"></i> {{ strtoupper(lang('settings', $translations)) }} <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu" style="{{ $style_child_menu }}">
                        @foreach ($library_menus as $config)
                            @if (Helper::authorizing($config['module'], $config['rule'])['status'] == 'true')
                                @php
                                    $menu_active = '';
                                    if(Helper::is_menu_active('/'.$config['prefix'])){
                                        $menu_active = 'current-page';
                                    }
                                @endphp
                                <li class="{{ $menu_active }}">
                                    <a href="{{ route($config['route_name'], $config['route_params']) }}">
                                        {{ $config['menu_label'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                  </li>
            @endif
        </ul>
    </div>

    @php
        $priv_admin = 0;
    @endphp
    <div class="menu_section" id="navmenu_admin" style="display:none">
        <hr>
        <h3>{{ ucwords(lang('dev tools', $translations)) }}</h3>
        <ul class="nav side-menu">
            @php
                $library_menus = [
                    'PHPINFO' => [
                        'module' => 'Dev Tools',
                        'rule' => '',
                        'prefix' => 'dev/phpinfo',
                        'route_name' => 'dev.phpinfo',
                        'route_params' => [],
                        'menu_label' => 'PHPINFO',
                    ],
                    'cheatsheet form' => [
                        'module' => 'Dev Tools',
                        'rule' => '',
                        'prefix' => 'dev/cheatsheet-form',
                        'route_name' => 'dev.cheatsheet_form',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('cheatsheet form', $translations)),
                    ],
                    'encrypt tool' => [
                        'module' => 'Dev Tools',
                        'rule' => '',
                        'prefix' => 'dev/encrypt',
                        'route_name' => 'dev.encrypt',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('encrypt tool', $translations)),
                    ],
                    'decrypt tool' => [
                        'module' => 'Dev Tools',
                        'rule' => '',
                        'prefix' => 'dev/decrypt',
                        'route_name' => 'dev.decrypt',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('decrypt tool', $translations)),
                    ],
                    'tester form' => [
                        'module' => 'Dev Tools',
                        'rule' => '',
                        'prefix' => 'dev/tester-form',
                        'route_name' => 'dev.tester_form',
                        'route_params' => [],
                        'menu_label' => ucwords(lang('tester form', $translations)),
                    ],
                ];

                $library_authorized = false;
                foreach ($library_menus as $config) {
                    if (Helper::authorizing($config['module'], $config['rule'])['status'] == 'true') {
                        $library_authorized = true;
                        $priv_admin++;
                        break;
                    }
                }
            @endphp
            @if ($library_authorized)
                @php
                    $parent_menu_active = '';
                    $style_child_menu = '';
                    foreach ($library_menus as $config) {
                        if(Helper::is_menu_active('/'.$config['prefix'])){
                            $parent_menu_active = 'current-page active';
                            $style_child_menu = 'display: block;';
                            break;
                        }
                    }
                @endphp
                <li class="{{ $parent_menu_active }}">
                    <a><i class="fa fa-desktop"></i> {{ strtoupper(lang('dev tools', $translations)) }} <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu" style="{{ $style_child_menu }}">
                        @foreach ($library_menus as $config)
                            @if (Helper::authorizing($config['module'], $config['rule'])['status'] == 'true')
                                @php
                                    $menu_active = '';
                                    if(Helper::is_menu_active('/'.$config['prefix'])){
                                        $menu_active = 'current-page';
                                    }
                                @endphp
                                <li class="{{ $menu_active }}">
                                    <a href="{{ route($config['route_name'], $config['route_params']) }}" {{ (isset($config['target'])) ? 'target="'.$config['target'].'"' : '' }}>
                                        {!! $config['menu_label'] !!}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                  </li>
            @endif

            @if (Helper::authorizing('Dev Tools', 'nav menu structure')['status'] == 'true')
                @php
                    $priv_admin++;
                @endphp
                <li>
                    <a href="{{ route('dev.nav_menu') }}" target="_blank">
                        <i class="fa fa-sitemap"></i> {{ ucwords(lang('nav menu structure', $translations)) }} &nbsp;<i class="fa fa-external-link pull-right"></i>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
<!-- /sidebar menu -->

@section('script-sidebar')
    <script>
        @if ($priv_admin > 0)
            $('#navmenu_admin').show();
        @endif
    </script>
@endsection