<?php

namespace App\Services\Ollama;

use App\Interfaces\OllamaClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Generator;

class OllamaClient implements OllamaClientInterface
{
    private Client $httpClient;
    private string $baseUrl;
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'base_url' => 'http://192.168.2.117:11434/api',
            'timeout' => 30,
            'default_model' => 'mistral:latest'
        ], $config);

        $this->baseUrl = $this->config['base_url'];
        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $this->config['timeout']
        ]);
    }

    public function generate(string $model, string $prompt, array $options = []): string
    {
        try {
            $response = $this->httpClient->post('/generate', [
                'json' => array_merge([
                    'model' => $model ?: $this->config['default_model'],
                    'prompt' => $prompt
                ], $options)
            ]);

            $responseBody = json_decode($response->getBody(), true);
            return $responseBody['response'] ?? '';
        } catch (RequestException $e) {
            throw new \Exception("Ollama generation failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function generateStream(string $model, string $prompt, array $options = []): Generator
    {
        try {
            $response = $this->httpClient->post('/generate', [
                'json' => array_merge([
                    'model' => $model ?: $this->config['default_model'],
                    'prompt' => $prompt,
                    'stream' => true
                ], $options)
            ]);

            while (!$response->getBody()->eof()) {
                $chunk = $response->getBody()->read(1024);
                if (!empty(trim($chunk))) {
                    yield $chunk;
                }
            }
        } catch (RequestException $e) {
            throw new \Exception("Ollama streaming generation failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function listModels(): array
    {
        try {
            $response = $this->httpClient->get('/tags');
            $models = json_decode($response->getBody(), true);
            return $models['models'] ?? [];
        } catch (RequestException $e) {
            throw new \Exception("Failed to list Ollama models: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function pullModel(string $modelName): bool
    {
        try {
            $response = $this->httpClient->post('/pull', [
                'json' => ['name' => $modelName]
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['status'] === 'success';
        } catch (RequestException $e) {
            throw new \Exception("Failed to pull Ollama model: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function hasModel(string $modelName): bool
    {
        $models = $this->listModels();
        return in_array($modelName, array_column($models, 'name'));
    }
}
