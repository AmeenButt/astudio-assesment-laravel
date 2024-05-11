<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

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
        //
        VerifyCsrfToken::except([
            "/api/user/delete",
            "/api/user",
            "/api/user/update",
            "/api/user/login",
            "/api/project",
            "/api/project/update",
            "/api/project/delete",
            "/api/timeSheet",
            "/api/timeSheet/update",
            "/api/timeSheet/delete"
        ]);
    }
}
