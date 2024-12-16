<?php

namespace App\Providers;

use App\Services\PrivateGPT\PrivateGPTService;
use Illuminate\Support\ServiceProvider;

class PrivateGPTServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PrivateGPTService::class, function ($app) {
            return new PrivateGPTService();
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/privategpt.php' => config_path('privategpt.php'),
        ], 'privategpt-config');
    }
}
