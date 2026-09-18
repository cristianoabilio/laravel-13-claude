<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Apply the visitor's chosen locale (persisted in session by the
     * language switcher) to the application for this request, falling back
     * to the configured default when none has been chosen or the stored
     * value is no longer a supported locale.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (is_string($locale) && array_key_exists($locale, config('locales.supported', []))) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
