<?php

namespace App\Providers;

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

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });
    }
}
