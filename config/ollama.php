<?php

return [
    'default_model' => env('OLLAMA_DEFAULT_MODEL', 'mistral:latest'),
    
    'models' => [
        'mistral' => [
            'name' => 'mistral:latest',
            'use_case' => 'General purpose reasoning',
            'max_tokens' => 4096,
            'temperature' => 0.7
        ],
        'llama2' => [
            'name' => 'llama2:latest',
            'use_case' => 'Conversational AI',
            'max_tokens' => 2048,
            'temperature' => 0.6
        ],
        'codellama' => [
            'name' => 'codellama:latest',
            'use_case' => 'Code generation',
            'max_tokens' => 8192,
            'temperature' => 0.5
        ]
    ],

    'api' => [
        'base_url' => env('OLLAMA_API_URL', 'http://192.168.2.117:11434/api'),
        'timeout' => env('OLLAMA_API_TIMEOUT', 30),
        'max_retries' => env('OLLAMA_API_MAX_RETRIES', 3)
    ],

    'features' => [
        'streaming' => true,
        'caching' => true,
        'error_logging' => true
    ]
];
