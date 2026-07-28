<?php

namespace App\Providers;

use App\Policies\InventarioPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar InventarioPolicy (sin modelo propio se mapea a sí misma)
        Gate::policy(InventarioPolicy::class, InventarioPolicy::class);
    }
}
