<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Registrar;
use App\Services\ResellerClubRegistrar;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Registrar::class,ResellerClubRegistrar::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
