<?php

return [
    'ollama' => [
        'endpoint' => env('OLLAMA_ENDPOINT', 'http://localhost:11434'),
        'models' => [
            'default' => 'codellama',
            'analysis' => 'codellama',
            'completion' => 'codellama',
            'review' => 'codellama',
        ],
    ],
    
    'private-gpt' => [
        'endpoint' => env('PRIVATE_GPT_ENDPOINT', 'http://localhost:8001'),
        'config' => [
            'max_tokens' => 2048,
            'temperature' => 0.7,
            'top_p' => 0.95,
        ],
    ],
    
    'cache' => [
        'ttl' => env('AI_CACHE_TTL', 3600),
        'prefix' => 'ai:',
    ],
    
    'limits' => [
        'max_code_size' => 50000, // characters
        'max_concurrent_requests' => 5,
        'rate_limit' => 60, // requests per minute
    ],
];