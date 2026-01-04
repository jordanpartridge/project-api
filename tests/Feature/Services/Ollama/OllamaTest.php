<?php

use App\Services\Ollama\OllamaService;
use App\Services\Ollama\OllamaResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->service = new OllamaService();
});

it('can generate response', function () {
    Http::fake([
        '*/api/generate' => Http::response([
            'model' => 'llama2',
            'response' => 'Test response',
            'done' => true
        ])
    ]);

    $response = $this->service->generate('Test prompt');

    expect($response)
        ->toBeInstanceOf(OllamaResponse::class)
        ->prompt->toBe('Test prompt')
        ->response->toBe('Test response');
});

it('caches responses', function () {
    $prompt = 'Test prompt';
    $cacheKey = 'ollama_' . md5($prompt . 'llama2' . '[]');

    Http::fake([
        '*/api/generate' => Http::response([
            'model' => 'llama2',
            'response' => 'Test response',
            'done' => true
        ])
    ]);

    $response = $this->service->generate($prompt);
    
    expect(Cache::has($cacheKey))->toBeTrue()
        ->and($this->service->getCachedResponse($cacheKey))
        ->toBeInstanceOf(OllamaResponse::class)
        ->response->toBe($response->response);
});

it('can list models', function () {
    Http::fake([
        '*/api/tags' => Http::response([
            'models' => [
                ['name' => 'llama2'],
                ['name' => 'codellama']
            ]
        ])
    ]);

    $models = $this->service->listModels();
    expect($models)->toBeArray();
});