<?php

namespace Tests\Feature\Services\Ollama;

use Tests\ServiceTestCase;
use App\Services\Ollama\OllamaService;
use App\Services\Ollama\OllamaResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OllamaServiceTest extends ServiceTestCase
{
    protected OllamaService $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OllamaService();
        Cache::flush(); // Clear cache before each test
    }

    public function test_it_can_generate_response()
    {
        Http::fake([
            '*/api/generate' => Http::response([
                'model' => 'llama2',
                'response' => 'Test response',
                'done' => true
            ])
        ]);

        $response = $this->service->generate('Test prompt');

        $this->assertInstanceOf(OllamaResponse::class, $response);
        $this->assertEquals('Test prompt', $response->prompt);
        $this->assertEquals('Test response', $response->response);
    }

    public function test_it_caches_responses()
    {
        $prompt = 'Cache test prompt';
        
        // First call should hit API
        Http::fake([
            '*/api/generate' => Http::response([
                'model' => 'llama2',
                'response' => 'API response',
                'done' => true
            ])
        ]);
        
        $firstResponse = $this->service->generate($prompt);
        
        // Second call should use cache
        Http::fake([
            '*/api/generate' => Http::response([
                'model' => 'llama2',
                'response' => 'Different response',
                'done' => true
            ])
        ]);
        
        $secondResponse = $this->service->generate($prompt);
        
        $this->assertEquals(
            $firstResponse->response,
            $secondResponse->response,
            'Second call should return cached response'
        );
        
        $this->assertEquals('API response', $secondResponse->response);
    }

    public function test_it_can_generate_code()
    {
        Http::fake([
            '*/api/generate' => Http::response([
                'model' => 'codellama',
                'response' => 'function test() { return true; }',
                'done' => true
            ])
        ]);

        $response = $this->service->generateCode('Write a test function');
        
        $this->assertInstanceOf(OllamaResponse::class, $response);
        $this->assertStringContainsString('function test()', $response->response);
    }
}