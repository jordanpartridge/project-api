<?php

namespace App\Services\Ollama;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class OllamaService 
{
    private $client;
    private $baseUrl;

    public function __construct() 
    {
        $this->baseUrl = env('OLLAMA_URL', 'http://localhost:11434');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 120.0,
        ]);
    }

    /**
     * Check if Ollama server is reachable
     */
    public function ping(): bool
    {
        try {
            $response = $this->client->get('/api/version');
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            Log::error('Ollama Ping Failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate text using Ollama
     */
    public function generate(string $model, string $prompt, array $options = []): string 
    {
        try {
            $requestBody = array_merge([
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                ]
            ], $options);

            $response = $this->client->post('/api/generate', [
                'json' => $requestBody
            ]);

            $data = json_decode($response->getBody(), true);

            return $data['response'] ?? 'No response generated.';
        } catch (\Exception $e) {
            Log::error('Ollama Generation Failed', [
                'model' => $model,
                'error' => $e->getMessage()
            ]);

            throw new \Exception("AI generation failed: " . $e->getMessage());
        }
    }

    /**
     * Generate streaming response from Ollama
     */
    public function generateStream(string $model, string $prompt, array $options = []): \Generator
    {
        try {
            $requestBody = array_merge([
                'model' => $model,
                'prompt' => $prompt,
                'stream' => true,
                'options' => [
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                ]
            ], $options);

            $response = $this->client->post('/api/generate', [
                'json' => $requestBody,
                'stream' => true
            ]);

            foreach ($response->getBody() as $chunk) {
                $data = json_decode($chunk, true);
                if (isset($data['response'])) {
                    yield $data['response'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Ollama Stream Generation Failed', [
                'model' => $model,
                'error' => $e->getMessage()
            ]);

            throw new \Exception("AI stream generation failed: " . $e->getMessage());
        }
    }

    /**
     * List available models
     */
    public function listModels(): array 
    {
        try {
            $response = $this->client->get('/api/tags');
            $data = json_decode($response->getBody(), true);
            return $data['models'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to list Ollama models', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}