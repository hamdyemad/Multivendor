<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLanguageCountry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $lang = $request->route('lang');
        $country = $request->route('countryCode');

        // Validate language exists in mcamara config:
        $supported = array_keys(config('laravellocalization.supportedLocales'));

        if (! in_array($lang, $supported)) {
            abort(404);
        }

        // Set locale normally
        app()->setLocale($lang);

        // Store country code in session (uppercase)
        if ($country) {
            session(['country_code' => strtoupper($country)]);
        }

        return $next($request);
    }

}
