<?php

namespace App\Providers;

use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\LanguageRepositoryInterface;
use App\Interfaces\UserInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\VendorInterface;
use App\Repositories\DepartmentRepository;
use App\Repositories\LanguageRepository;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Repositories\VendorRepository;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Observers\GlobalModelObserver;
use App\Models\ActivityLog;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Models to exclude from logging
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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Register observers for all models
        $this->registerModelObservers();
    }

    /**
     * Register observers for all discovered models
     */
    private function registerModelObservers(): void
    {
        $models = $this->getCachedModels();
        
        foreach ($models as $model) {
            if (!in_array($model, $this->excludedModels) && class_exists($model)) {
                try {
                    $model::observe(GlobalModelObserver::class);
                } catch (\Exception $e) {
                    // Log the error but don't stop the application
                    \Log::warning("Could not observe model: {$model}. Error: " . $e->getMessage());
                    continue;
                }
            }
        }
    }

    /**
     * Get cached models or discover them
     */
    private function getCachedModels(): array
    {
        // In production, cache the models. In development, always rediscover
        if ($this->app->environment('production')) {
            return Cache::rememberForever('app.discovered_models', function () {
                return $this->getAllModels();
            });
        }
        
        return $this->getAllModels();
    }

    /**
     * Get all models in the application
     */
    private function getAllModels(): array
    {
        $models = [];
        
        // Get models from app/Models directory
        $models = array_merge($models, $this->getModelsFromDirectory(app_path('Models'), 'App\\Models'));
        
        // Get models from Modules (for modular Laravel apps)
        if (File::exists(base_path('Modules'))) {
            $modules = File::directories(base_path('Modules'));
            
            foreach ($modules as $module) {
                $moduleName = basename($module);
                
                // Check multiple possible paths for models
                $possiblePaths = [
                    $module . '/app/Models',
                    $module . '/Models',
                    $module . '/Entities',
                ];
                
                foreach ($possiblePaths as $modelsPath) {
                    if (File::exists($modelsPath)) {
                        $namespace = $this->getNamespaceForPath($modelsPath, $moduleName);
                        $models = array_merge(
                            $models,
                            $this->getModelsFromDirectory($modelsPath, $namespace)
                        );
                    }
                }
            }
        }
        
        return array_unique($models);
    }

    /**
     * Get namespace for a given path
     */
    private function getNamespaceForPath(string $path, string $moduleName): string
    {
        $baseNamespace = "Modules\\{$moduleName}\\";
        
        if (str_contains($path, '/app/Models')) {
            return $baseNamespace . 'app\\Models';
        } elseif (str_contains($path, '/Models')) {
            return $baseNamespace . 'Models';
        } else {
            return $baseNamespace . 'Entities';
        }
    }

    /**
     * Get all model classes from a directory
     */
    private function getModelsFromDirectory(string $path, string $namespace): array
    {
        $models = [];
        
        if (!File::exists($path)) {
            return $models;
        }
        
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            $relativePath = str_replace($path, '', $file->getRealPath());
            $relativePath = str_replace('.php', '', $relativePath);
            $relativePath = str_replace('/', '\\', $relativePath);
            $relativePath = ltrim($relativePath, '\\');
            
            $class = $namespace . '\\' . $relativePath;
            
            try {
                if (class_exists($class)) {
                    $reflection = new \ReflectionClass($class);
                    
                    // Only include classes that extend Eloquent Model and are not abstract
                    if (
                        $reflection->isSubclassOf(\Illuminate\Database\Eloquent\Model::class) &&
                        !$reflection->isAbstract()
                    ) {
                        $models[] = $class;
                    }
                }
            } catch (\Exception $e) {
                // Skip classes that can't be reflected
                continue;
            }
        }
        
        return $models;
    }
}