<?php

namespace App\Http\Middleware;

use Closure;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\AreaSettings\app\Models\Country;
use App\Providers\RouteServiceProvider;

class AutoCountryAndLocaleRedirect
{
    public function handle($request, Closure $next)
    {
        $path = trim($request->getPathInfo(), '/');

        // Set default country code in session if not already set
        if (!session()->has('country_code')) {
            $country = Country::default()->first() ?? Country::first();
            if ($country) {
                session()->put('country_code', $country->code);
            }
        }

        // If root path and authenticated, redirect to appropriate dashboard based on user type
        if (empty($path) && auth()->check()) {
            $user = auth()->user();
            $countryCode = 'eg'; // default
            $locale = app()->getLocale() ?? 'en';

            // For admin users (type 1 = SUPER_ADMIN, type 2 = ADMIN), use selected country from session
            if ($user->user_type_id == 1 || $user->user_type_id == 2) {
                $countryCode = session('country_code') ?? 'eg';
            }
            // For vendor users (type 3 = VENDOR or type 4 = VENDOR_USER), use their vendor's country
            elseif ($user->user_type_id == 3 || $user->user_type_id == 4) {
                if ($user->vendor && $user->vendor->country) {
                    $countryCode = strtolower($user->vendor->country->code);
                } else {
                    // Fallback to session country if vendor doesn't have a country
                    $countryCode = strtolower(session('country_code', 'eg'));
                }
            }

            $homePath = '/' . $locale . '/' . strtolower($countryCode) . '/admin/dashboard';
            return redirect($homePath);
        }

        // Skip for empty paths (not authenticated)
        if (empty($path)) {
            return $next($request);
        }

        $segments = explode('/', $path);
        $supportedLanguages = LaravelLocalization::getSupportedLanguagesKeys();

        // Check if path already has locale/countryCode format (e.g., /en/eg/admin/...)
        if (count($segments) >= 2) {
            $firstSegment = strtolower($segments[0]);
            $secondSegment = strtoupper($segments[1]);

            // Check if first segment is a valid locale
            if (in_array($firstSegment, $supportedLanguages)) {
                // Check if second segment is a valid country code
                $country = Country::where('code', $secondSegment)->first();
                if ($country) {
                    // For vendor users: enforce their vendor's country
                    if (auth()->check()) {
                        $user = auth()->user();
                        // Vendor or Vendor User (type 3 or 4)
                        if (($user->user_type_id == 3 || $user->user_type_id == 4) && $user->vendor && $user->vendor->country) {
                            $vendorCountryCode = strtoupper($user->vendor->country->code);

                            // If URL country doesn't match vendor's country, redirect to vendor's country
                            if ($secondSegment !== $vendorCountryCode) {
                                // Rebuild path with vendor's country
                                $segments[1] = strtolower($vendorCountryCode);
                                $newPath = '/' . implode('/', $segments);
                                session()->put('country_code', $vendorCountryCode);
                                return redirect($newPath);
                            }
                        }
                    }

                    // Already in correct format: /locale/countryCode/...
                    session()->put('country_code', $country->code);
                    return $next($request);
                }

                // First segment is locale but second is NOT a country code
                // Need to inject country code after locale
                // e.g., /en/admin/... → /en/eg/admin/...
                $defaultCountry = Country::where('code', session('country_code'))->first()
                    ?? Country::default()->first()
                    ?? Country::first();

                if ($defaultCountry) {
                    // Remove locale from segments and rebuild
                    array_shift($segments); // Remove locale
                    $restOfPath = implode('/', $segments);

                    $newPath = '/' . $firstSegment . '/' . strtolower($defaultCountry->code);
                    if (!empty($restOfPath)) {
                        $newPath .= '/' . $restOfPath;
                    }

                    session()->put('country_code', $defaultCountry->code);
                    return redirect($newPath);
                }
            }
        }

        // Check if first segment is just a locale (e.g., /en or /ar)
        if (count($segments) >= 1) {
            $firstSegment = strtolower($segments[0]);
            if (in_array($firstSegment, $supportedLanguages)) {
                $defaultCountry = Country::where('code', session('country_code'))->first()
                    ?? Country::default()->first()
                    ?? Country::first();

                if ($defaultCountry) {
                    array_shift($segments); // Remove locale
                    $restOfPath = implode('/', $segments);

                    $newPath = '/' . $firstSegment . '/' . strtolower($defaultCountry->code);
                    if (!empty($restOfPath)) {
                        $newPath .= '/' . $restOfPath;
                    }

                    session()->put('country_code', $defaultCountry->code);
                    return redirect($newPath);
                }
            }
        }

        // Get default country and locale
        $country = Country::default()->first() ?? Country::first();
        $locale = LaravelLocalization::getCurrentLocale() ?? 'en';

        // Redirect to /locale/countryCode/path
        $newPath = '/' . $locale . '/' . strtolower($country->code);
        if (!empty($path)) {
            $newPath .= '/' . $path;
        }
        session()->put('country_code', $country->code);

        return redirect($newPath);
    }
}
