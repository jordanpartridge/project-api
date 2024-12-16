<?php

return [
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
    ],
    
    'logs' => [
        'repair_progress' => storage_path('logs/test-repair-progress.log'),
        'repair_outcomes' => storage_path('logs/test-repair-outcomes.log'),
    ],
    
    'patterns' => [
        'undefined_method' => [
            'pattern' => '/Call to undefined method ([^:]+)::([^(]+)/',
            'handler' => 'handleUndefinedMethod'
        ],
        'class_not_found' => [
            'pattern' => '/Class \'([^\']+)\' not found/',
            'handler' => 'handleClassNotFound'
        ],
        'assertion_failed' => [
            'pattern' => '/Failed asserting that (.+)/',
            'handler' => 'handleAssertionFailure'
        ]
    ]
];
