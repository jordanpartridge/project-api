<?php

use App\Services\Ollama\OllamaService;
use Illuminate\Support\Facades\Config;

it('registers ollama service', function () {
    expect(app()->bound(OllamaService::class))->toBeTrue()
        ->and(app(OllamaService::class))->toBeInstanceOf(OllamaService::class);
});

it('loads ollama config', function () {
    expect(Config::has('services.ollama'))->toBeTrue()
        ->and(Config::get('services.ollama.url'))->toBe('http://localhost:11434')
        ->and(Config::get('services.ollama.default_model'))->toBe('llama2')
        ->and(Config::get('services.ollama.cache_duration'))->toBe(3600);
});