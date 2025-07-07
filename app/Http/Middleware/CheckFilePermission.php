<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckFilePermission
{
    public function handle(Request $request, Closure $next)
    {
        if (env('FILE_PERMISSION', false)) {
            if ($this->hasPermissionToViewImage()) {
                return $next($request); // Jika diizinkan, lanjutkan request
            }

            return response()->json(['error' => 'Access Denied'], 403); // Jika tidak diizinkan
        }

        return $next($request);
    }

    protected function hasPermissionToViewImage()
    {
        if (Session::has(env('SESSION_ADMIN_NAME', 'sysadmin'))) {
            if (env('2FA_ENABLED', false)) {
                if (Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'))->google2fa_enabled) {
                    if (isset(Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'))->google2fa_passed) && !Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'))->google2fa_passed) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
}
