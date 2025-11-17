<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Persist the selected locale into the session and redirect back.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $availableLocales = config('app.available_locales', ['en', 'id']);

        if (!in_array($locale, $availableLocales, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        return redirect()->back();
    }
}
