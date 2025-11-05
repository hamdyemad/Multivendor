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
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        // if (config('app.env') === 'production') {
        // }
        URL::forceScheme('https');
    }
}
