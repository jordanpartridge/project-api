<?php

namespace App\Providers;

use App\Services\Prism\ModelDocumentationService;
use Illuminate\Support\ServiceProvider;
use OpenAI\Client;

class PrismServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            return new Client(['api_key' => config('services.openai.key')]);
        });

        $this->app->singleton(ModelDocumentationService::class, function ($app) {
            return new ModelDocumentationService($app->make(Client::class));
        });
    }

    public function boot()
    {
        //
    }
}
