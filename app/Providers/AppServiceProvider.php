<?php

namespace App\Providers;

use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\LanguageRepositoryInterface;
use App\Interfaces\UserInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Repositories\LanguageRepository;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Database\Eloquent\Model;
use App\Observers\GlobalModelObserver;
use App\Observers\CountryGlobalObserver;
use App\Observers\CountryObserver;
use App\Models\ActivityLog;
use Modules\AreaSettings\app\Models\Country;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Models to exclude from observers
     */
    protected $excludedModels = [
        ActivityLog::class,
        \Illuminate\Notifications\DatabaseNotification::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Register Blade directive for admin routes with lang and country
        // Note: URL defaults are set by SetAdminRouteDefaults middleware
        // so route() helper will automatically include lang and country

        // Register observers for App models
        $this->registerAppModelObservers();

        // Register observers for Module models
        $this->registerModuleModelObservers();
    }

    /**
     * Register observers for all App models
     */
    private function registerAppModelObservers(): void
    {
        try {
            foreach (File::allFiles(app_path('Models')) as $modelFile) {
                $modelClass = 'App\\Models\\' . $modelFile->getBasename('.php');
                if (class_exists($modelClass) && is_subclass_of($modelClass, Model::class)) {
                    $modelClass::observe(GlobalModelObserver::class);
                    $modelClass::observe(CountryObserver::class);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error registering App model observers: ' . $e->getMessage());
        }
    }

    /**
     * Register observers for all Module models
     */
    private function registerModuleModelObservers(): void
    {
        try {
            $modulesPath = base_path('Modules');
            if (!File::exists($modulesPath)) {
                return;
            }

            // Use cache to avoid scanning on every request
            $cacheKey = 'module_models_observers_registered';

            if (Cache::has($cacheKey)) {
                return;
            }

            foreach (File::directories($modulesPath) as $module) {
                $moduleName = basename($module);
                $modelsPath = $module . '/app/Models';

                if (File::exists($modelsPath)) {
                    foreach (File::allFiles($modelsPath) as $modelFile) {
                        $modelClass = "Modules\\{$moduleName}\\app\\Models\\" . $modelFile->getBasename('.php');
                        if (class_exists($modelClass) && is_subclass_of($modelClass, Model::class)) {
                            $modelClass::observe(CountryObserver::class);
                            $modelClass::observe(GlobalModelObserver::class);
                        }
                    }
                }
            }

            // Mark as registered in cache (expires in 24 hours)
            Cache::put($cacheKey, true, 86400);
        } catch (\Exception $e) {
            Log::warning('Error registering Module model observers: ' . $e->getMessage());
        }

    }

}
