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
    public const HOME = '/admin/dashboard';

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
            // API routes MUST be registered first and without any localization or URL defaults
            Route::middleware('api')
                ->prefix('api')
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
                ->group(function() {
                    require base_path('routes/admin.php');
                });
        });

        // Set URL defaults after routes are registered
        // $this->app->booted(function () {
        //     $this->setUrlDefaults();
        // });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
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
