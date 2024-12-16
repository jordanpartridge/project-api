<?php

namespace App\Providers;

use App\Services\CodeAnalysis\PrismAnalyzer;
use Illuminate\Support\ServiceProvider;

class AnalysisServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PrismAnalyzer::class, function ($app) {
            return new PrismAnalyzer(base_path());
        });
    }
}