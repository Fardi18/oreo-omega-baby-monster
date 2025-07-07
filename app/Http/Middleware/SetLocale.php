<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->segment(1); // ex: "jp"
        $supportedLocales = ['en', 'id', 'th', 'vi'];

        if (!in_array($locale, $supportedLocales)) {
            $segments = $request->segments();
            $segments[0] = 'en'; // fallback default

            return redirect()->to('/' . implode('/', $segments) . '?invalid_lang=1');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
