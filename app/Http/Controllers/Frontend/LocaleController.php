<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch the visitor's language and send them back to the page they
     * were on. The locale is persisted in session so it sticks across the
     * whole visit without needing a locale segment in every URL.
     */
    public function update(string $locale, Request $request): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('locales.supported', [])), 404);

        $request->session()->put('locale', $locale);

        return redirect()->back();
    }
}
