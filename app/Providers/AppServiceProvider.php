<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path() . '/Helpers/helpers.php';
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        setlocale(LC_TIME, 'fr_FR.UTF-8');
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();
    }
}
