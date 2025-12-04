<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SetAdminRouteDefaults
{
    /**
     * Handle an incoming request.
     *
     * Sets URL defaults for lang and country so route() helper automatically includes them
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get lang and country from route parameters
        $lang = $request->route('lang') ?? app()->getLocale() ?? 'en';
        $countryCode = $request->route('countryCode') ?? strtolower(session('country_code', 'eg'));

        // Set URL defaults so route() helper automatically includes these parameters
        URL::defaults([
            'lang' => $lang,
            'countryCode' => $countryCode,
        ]);

        return $next($request);
    }
}
