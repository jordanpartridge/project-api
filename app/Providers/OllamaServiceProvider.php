<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\OllamaClientInterface;
use App\Services\Ollama\OllamaClient;

class OllamaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(OllamaClientInterface::class, function ($app) {
            // Read configuration from config/ollama.php
            $config = config('ollama', []);
            return new OllamaClient($config['api'] ?? []);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Any additional bootstrapping logic
    }
}
