<?php

namespace App\Providers;

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
        // Redirect after login based on role
        \Illuminate\Support\Facades\Route::bind('smart_home', function () {
            return match (auth()->user()?->account_type) {
                'admin'    => redirect()->route('admin.dashboard'),
                'employer' => redirect()->route('employer.dashboard'),
                default    => redirect()->route('employee.dashboard'),
            };
        });
    }
}
