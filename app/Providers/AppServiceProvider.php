<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Monolog\Formatter\LineFormatter;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Enhance logging with more detailed formatting
        $monolog = Log::getMonolog();
        $formatter = new LineFormatter(
            '[%datetime%] %channel%.%level_name%: %message% %context% %extra%',
            'Y-m-d H:i:s',
            true,
            true
        );

        foreach ($monolog->getHandlers() as $handler) {
            $handler->setFormatter($formatter);
        }
    }
}