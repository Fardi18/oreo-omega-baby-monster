<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Session::has(env('SESSION_ADMIN_NAME', 'sysadmin'))) {
            if (env('2FA_ENABLED', false)) {
                if (Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'))->google2fa_enabled) {
                    if (isset(Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'))->google2fa_passed) && !Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'))->google2fa_passed) {
                        // redirect to admin login page because not auth with 2FA
                        return redirect()->route('admin.login.verify_2fa')->with('warning', lang('Enter your 2FA code first!'));
                    }
                }
            }

            return $next($request);
        } else {
            // get actual link
            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            // validate actual link is AJAX URL
            if (strpos($actual_link, '/get-data') !== false || strpos($actual_link, '/store') !== false || strpos($actual_link, '/update') !== false) {
                // if it is AJAX URL, then set redirect uri to admin homepage
                $actual_link = route('admin.home');
            }

            // store redirect uri to session
            Session::put('redirect_uri_admin', $actual_link);

            if (substr($actual_link, -1) == '/') {
                $actual_link = substr($actual_link, 0, -1);
            }

            // if actual link is admin homepage, then redirect to admin login page
            if ($actual_link == route('admin.home')) {
                return redirect()->route('admin.login');
            }

            // redirect to admin login page with warning message
            return redirect()->route('admin.login')->with('warning', lang('You must login first!'));
        }
    }
}
