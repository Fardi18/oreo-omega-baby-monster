<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

// Models
use App\Models\country;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $marketAlias = strtolower($request->segment(1));
        $locale = strtolower($request->segment(2));

        // Check if user is logged in via session
        $userMarket = Session::get(env('SESSION_MARKET', 'user_market'));

        if ($userMarket) {
            // If trying to access different market, silently redirect to user's market
            if (strtolower($marketAlias) !== strtolower($userMarket)) {
                // Get all segments after market and locale
                $segments = $request->segments();
                $remainingPath = array_slice($segments, 2);

                // Build the new path with user's market but keep the rest of the URL
                $newPath = '/' . strtolower($userMarket) . '/' . $locale;
                if (!empty($remainingPath)) {
                    $newPath .= '/' . implode('/', $remainingPath);
                }

                return redirect($newPath);
            }
        }

        if ($marketAlias === 'en') {
            // Treat "en" as a default virtual market
            $country = new \stdClass();
            $country->id = 0;
            $country->country_alias = 'EN';
            $country->country_name = 'Default Market';
        } else {
            $country = country::where('country_alias', strtoupper($marketAlias))->first();

            if (!$country) {
                return redirect('/en/en')->with('error', 'Invalid market');
            }
        }

        // Validasi bahasa sesuai market
        $validLanguages = config('market')[$marketAlias] ?? ['en'];

        if (!in_array($locale, $validLanguages)) {
            $locale = $validLanguages[0]; // fallback ke default
            return redirect("/$marketAlias/$locale")->with('error', 'Invalid language for this market');
        }

        App::setLocale($locale);

        // inject data ke request (opsional)
        $request->attributes->add([
            'market' => $country,
            'locale' => $locale
        ]);

        return $next($request);
    }
}
