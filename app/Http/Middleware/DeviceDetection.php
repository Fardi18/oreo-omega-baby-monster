<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Libraries\TheHelper;

class DeviceDetection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip device check for certain paths
        $excludedPaths = [
            'desktop',      // Don't check on desktop page
            'api',         // Skip for API routes
            env('ADMIN_DIR', 'superuser'), // Skip for admin routes
        ];

        // Check if current path starts with any excluded path
        foreach ($excludedPaths as $path) {
            if (!empty($path) && $request->is($path . '*')) {
                return $next($request);
            }
        }

        // Check if mobile
        if (!TheHelper::is_mobile()) {
            // Store the intended URL in session
            session()->put('intended_url', $request->url());

            // Redirect to desktop page
            return redirect()->route('desktop.index');
        }

        return $next($request);
    }
}
