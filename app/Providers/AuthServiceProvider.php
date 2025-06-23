<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('viewPulse', function ($user) {
            return app()->environment('production')
                ? $user->admin == 1
                : true;
        });
    }
}
