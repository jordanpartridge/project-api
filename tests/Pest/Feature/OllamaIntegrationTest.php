<?php

use App\Services\Ollama\OllamaService;
use Pest\Exceptions\PendingTest;

describe('Ollama Integration', function () {
    it('can connect to Ollama server', function () {
        $service = new OllamaService();
        
        expect($service->ping())
            ->toBeTrue('Ollama server should be reachable');
    });

    it('can list available models', function () {
        $service = new OllamaService();
        
        $models = $service->listModels();
        
        expect($models)
            ->toBeArray('Models should be returned as an array')
            ->not()->toBeEmpty('At least one model should be available');
    });

    it('can generate text from a prompt', function () {
        $service = new OllamaService();
        
        $response = $service->generate('llama2', 'Explain machine learning in two sentences');
        
        expect($response)
            ->toBeString('Response should be a string')
            ->not()->toBeEmpty('Response should contain content');
    });

    it('handles model inference with different models', function () {
        $testModels = ['llama2', 'mistral:latest', 'llama2:13b'];
        
        $service = new OllamaService();
        
        foreach ($testModels as $model) {
            $response = $service->generate($model, 'What is your purpose?');
            
            expect($response)
                ->toBeString("Model $model should return a string response")
                ->not()->toBeEmpty("Model $model should provide a non-empty response");
        }
    });

    it('implements proper error handling', function () {
        $service = new OllamaService();
        
        expect(fn() => $service->generate('non-existent-model', 'Test prompt'))
            ->toThrow(Exception::class, 'Should throw an exception for non-existent models');
    });

    it('supports streaming responses', function () {
        $service = new OllamaService();
        
        $stream = $service->generateStream('llama2', 'Describe the future of AI');
        
        expect($stream)
            ->toBeIterable('Stream should be an iterable')
            ->not()->toBeEmpty('Stream should contain response chunks');
    });
});