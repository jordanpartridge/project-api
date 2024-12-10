<?php

return [
    // ... other service configs ...

    'prism' => [
        'endpoint' => env('PRISM_ENDPOINT', 'https://api.anthropic.com/v1/messages'),
        'key' => env('PRISM_API_KEY'),
    ],
];
