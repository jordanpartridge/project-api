<?php

namespace App\Providers;

use App\Http\Integrations\Github\Github;
use Illuminate\Support\ServiceProvider;

class GithubServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(Github::class, function () {
            return new Github(config('services.github.token'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
