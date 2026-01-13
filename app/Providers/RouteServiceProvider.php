<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\AreaSettings\app\Models\Country;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = "en/eg/admin/dashboard";

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // API routes with v1 versioning prefix
            // All API endpoints will be accessible at /api/v1/...
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/api.php'));

            // Web routes with localization
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Admin routes with authentication and country code
            Route::middleware('web', 'auth', 'setLanguageCountry',
            'setAdminRouteDefaults',
            'localizationRedirect',
            'localeViewPath')
                ->as('admin.')
                ->prefix('{lang}/{countryCode}/admin')
                ->where(['lang' => '[a-z]{2}', 'countryCode' => '[a-z]{2,3}'])
                ->group(function() {
                    require base_path('routes/admin.php');
                });
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        // Default API rate limit: 60 requests per minute
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(600000000)->by($request->user()?->id ?: $request->ip());
        });

        // Higher rate limit for product APIs (public endpoints with high traffic)
        RateLimiter::for('products', function (Request $request) {
            return Limit::perMinute(600000000)->by($request->user()?->id ?: $request->ip());
        });

        // Strict rate limit for authentication endpoints (prevent brute-force)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(600000000)->by($request->ip());
        });

        // Rate limit for OTP/verification endpoints
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(600000000)->by($request->ip());
        });

        // Rate limit for checkout/payment endpoints
        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(600000000)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limit for vendor requests
        RateLimiter::for('vendor-request', function (Request $request) {
            return Limit::perMinute(600000000)->by($request->ip());
        });
    }

    /**
     * Set URL defaults for locale and country code
     * This ensures route() helper automatically includes these parameters
     * URL format: /{locale}/{countryCode}/admin/...
     *
     * @return void
     */
    // protected function setUrlDefaults()
    // {
    //     try {
    //         // Get country code from URL segment (segment 2 is after locale)
    //         $urlCountryCode = request()->segment(2);

    //         // Validate if it's a real country code (2 letters)
    //         $countryCode = null;
    //         if ($urlCountryCode && strlen($urlCountryCode) === 2) {
    //             $country = Country::where('code', strtoupper($urlCountryCode))->first();
    //             if ($country) {
    //                 $countryCode = strtolower($urlCountryCode);
    //                 // Store in session for future use
    //                 session(['country_code' => $country->code]);
    //             }
    //         }

    //         // Fallback to session or default
    //         if (!$countryCode) {
    //             $countryCode = strtolower(session('country_code', 'eg'));
    //         }

    //         // Set URL defaults so route() automatically includes country code
    //         URL::defaults([
    //             'countryCode' => $countryCode,
    //         ]);

    //     } catch (\Exception $e) {
    //         // Fallback
    //         URL::defaults([
    //             'countryCode' => 'eg',
    //         ]);
    //     }
    // }
}
