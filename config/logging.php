<?php

return [
    'default' => env('LOG_CHANNEL', 'stack'),
    'level' => env('LOG_LEVEL', 'debug'),
    
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'slack'],
            'ignore_exceptions' => false,
        ],
        
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Project API Logs',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],
        
        'github_integration' => [
            'driver' => 'daily',
            'path' => storage_path('logs/github-integration.log'),
            'level' => 'info',
        ],
        
        'api_requests' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api-requests.log'),
            'level' => 'info',
        ],

        // Existing Laravel default channels
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => Monolog\Handler\StreamHandler::class,
            'formatter' => Monolog\Formatter\JsonFormatter::class,
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],
    ],
];
