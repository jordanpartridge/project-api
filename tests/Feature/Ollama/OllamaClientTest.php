<?php

namespace Tests\Feature\Ollama;

use App\Interfaces\OllamaClientInterface;
use Tests\TestCase;

class OllamaClientTest extends TestCase
{
    private OllamaClientInterface $ollamaClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ollamaClient = $this->app->make(OllamaClientInterface::class);
    }

    /** @test */
    public function it_can_list_available_models()
    {
        $models = $this->ollamaClient->listModels();
        
        $this->assertIsArray($models);
        $this->assertNotEmpty($models);
        
        // Check model structure
        foreach ($models as $model) {
            $this->assertArrayHasKey('name', $model);
            $this->assertArrayHasKey('model', $model);
        }
    }

    /** @test */
    public function it_can_generate_text_response()
    {
        $prompt = "Write a one-paragraph explanation of machine learning.";
        $response = $this->ollamaClient->generate('mistral:latest', $prompt);
        
        $this->assertIsString($response);
        $this->assertNotEmpty($response);
    }

    /** @test */
    public function it_can_stream_text_response()
    {
        $prompt = "Explain the basics of quantum computing in a few sentences.";
        $streamGenerator = $this->ollamaClient->generateStream('mistral:latest', $prompt);
        
        $fullResponse = '';
        foreach ($streamGenerator as $chunk) {
            $this->assertIsString($chunk);
            $fullResponse .= $chunk;
        }
        
        $this->assertNotEmpty($fullResponse);
    }

    /** @test */
    public function it_checks_model_availability()
    {
        $availableModel = 'mistral:latest';
        $unavailableModel = 'non-existent-model';

        $this->assertTrue($this->ollamaClient->hasModel($availableModel));
        $this->assertFalse($this->ollamaClient->hasModel($unavailableModel));
    }
}
