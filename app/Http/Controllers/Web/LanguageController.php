<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $marketId = $request->input('market_id');
        $refererUrl = $request->input('referer_url'); // ambil path dari halaman sebelumnya

        if (!$marketId || !is_numeric($marketId) || !$refererUrl) {
            return redirect()->back();
        }

        $country = \App\Models\country::find($marketId);
        if (!$country) {
            return redirect()->back()->withErrors(['market_id' => 'Invalid country code']);
        }

        $marketAlias = strtolower($country->country_alias);
        $availableLangs = config('market')[$marketAlias] ?? ['en'];
        $defaultLang = $availableLangs[0];

        // Pisahkan segment path URL asal
        $segments = explode('/', trim($refererUrl, '/'));

        // Pastikan ada cukup segment
        if (count($segments) >= 2) {
            $segments[0] = $marketAlias;
            $segments[1] = $defaultLang;
        } else {
            array_unshift($segments, $defaultLang);
            array_unshift($segments, $marketAlias);
        }

        $newUrl = '/' . implode('/', $segments);

        // Flash input jika perlu
        $formData = $request->except('_token');
        session()->flash('_old_input', $formData);

        return redirect($newUrl);
    }

    public function switch_backup(Request $request)
    {
        $marketId = $request->input('market_id');

        // if market_id is not provided or is not numeric, redirect back
        if (!$marketId || !is_numeric($marketId)) {
            return redirect()->back(); // you can also return an error message here
        }

        $country = \App\Models\country::find($marketId);
        if (!$country) {
            return redirect()->back()->withErrors(['market_id' => 'Invalid country code']);
        }

        $marketAlias = strtolower($country->country_alias);
        $availableLangs = config('market')[$marketAlias] ?? ['en'];
        $defaultLang = $availableLangs[0];

        $formData = $request->except('_token');
        session()->flash('_old_input', $formData);

        return redirect("/$marketAlias/$defaultLang");
    }
}
