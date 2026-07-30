<?php

namespace App\Providers;

use App\Models\Adjunto;
use App\Policies\AdjuntoPolicy;
use App\Policies\InventarioPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(InventarioPolicy::class, InventarioPolicy::class);
        Gate::policy(Adjunto::class, AdjuntoPolicy::class);

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });
    }
}
