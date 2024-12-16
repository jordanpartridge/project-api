<?php

return [
    'url' => env('PRIVATE_GPT_URL', 'http://localhost:8001'),
    'model' => env('PRIVATE_GPT_MODEL', 'default'),
    'embedding_model' => env('PRIVATE_GPT_EMBEDDING_MODEL', 'all-MiniLM-L6-v2'),
    'chunk_size' => env('PRIVATE_GPT_CHUNK_SIZE', 512),
];
