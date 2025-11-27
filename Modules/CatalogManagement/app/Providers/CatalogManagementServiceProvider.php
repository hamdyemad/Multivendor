<?php

namespace Modules\CatalogManagement\app\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\CatalogManagement\app\Interfaces\BrandRepositoryInterface;
use Modules\CatalogManagement\app\Repositories\BrandRepository;
use Modules\CatalogManagement\app\Interfaces\TaxRepositoryInterface;
use Modules\CatalogManagement\app\Repositories\TaxRepository;
use Modules\CatalogManagement\app\Interfaces\VariantConfigurationKeyRepositoryInterface;
use Modules\CatalogManagement\app\Repositories\VariantConfigurationKeyRepository;
use Modules\CatalogManagement\app\Interfaces\ProductInterface;
use Modules\CatalogManagement\app\Repositories\ProductRepository;
use Modules\CatalogManagement\app\Interfaces\PricingStockRepositoryInterface;
use Modules\CatalogManagement\app\Repositories\PricingStockRepository;
use Modules\CatalogManagement\app\Interfaces\Api\ProductApiRepositoryInterface;
use Modules\CatalogManagement\app\Repositories\Api\ProductApiRepository;
use Modules\CatalogManagement\app\Services\Api\ProductApiService;
use Modules\CatalogManagement\app\Actions\ProductQueryAction;
use App\Actions\IsPaginatedAction;
use Modules\CategoryManagment\app\Services\Api\CategoryApiService;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CatalogManagementServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'CatalogManagement';

    protected string $nameLower = 'catalogmanagement';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        // Register repository bindings
        $this->app->bind(
            BrandRepositoryInterface::class,
            BrandRepository::class
        );

        $this->app->bind(
            TaxRepositoryInterface::class,
            TaxRepository::class
        );

        $this->app->bind(
            VariantConfigurationKeyRepositoryInterface::class,
            VariantConfigurationKeyRepository::class
        );

        $this->app->bind(
            ProductInterface::class,
            ProductRepository::class
        );

        // Register API repository and service bindings
        $this->app->singleton(ProductQueryAction::class);

        $this->app->singleton(IsPaginatedAction::class);

        $this->app->bind(
            ProductApiRepositoryInterface::class,
            function ($app) {
                return new ProductApiRepository(
                    $app->make(ProductQueryAction::class),
                    $app->make(IsPaginatedAction::class),
                    $app->make(CategoryApiService::class)
                );
            }
        );

        $this->app->singleton(
            ProductApiService::class,
            function ($app) {
                return new ProductApiService(
                    $app->make(ProductApiRepositoryInterface::class)
                );
            }
        );
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace').'\\' . $this->name . '\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
