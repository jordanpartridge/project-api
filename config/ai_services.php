<?php

return [
    'default_service' => env('AI_DEFAULT_SERVICE', 'ollama'),
    
    'services' => [
        'ollama' => [
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'default_model' => env('OLLAMA_DEFAULT_MODEL', 'codellama:7b')
        ]
    ]
];
