<?php

namespace App\Services;

use App\Integrations\Ollama\OllamaConnector;
use App\Integrations\Ollama\Requests\GenerateTextRequest;
use Illuminate\Support\Facades\Log;
use Saloon\Exceptions\Request\ClientException;
use Saloon\Exceptions\Request\ServerException;

class OllamaService 
{
    private OllamaConnector $connector;

    public function __construct() 
    {
        $this->connector = new OllamaConnector();
    }

    /**
     * Generate text using Ollama
     */
    public function generate(string $model, string $prompt, array $options = []): string 
    {
        try {
            $request = new GenerateTextRequest($model, $prompt, $options);
            $response = $this->connector->send($request);

            return $response['response'] ?? 'No response generated.';
        } catch (ClientException $e) {
            Log::error('Ollama Client Error', [
                'model' => $model,
                'error' => $e->getMessage(),
                'response' => $e->getResponse()->json()
            ]);

            throw new \Exception("AI generation client error: " . $e->getMessage());
        } catch (ServerException $e) {
            Log::error('Ollama Server Error', [
                'model' => $model,
                'error' => $e->getMessage(),
                'response' => $e->getResponse()->json()
            ]);

            throw new \Exception("AI generation server error: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Ollama Generation Unexpected Error', [
                'model' => $model,
                'error' => $e->getMessage()
            ]);

            throw new \Exception("Unexpected AI generation error: " . $e->getMessage());
        }
    }

    /**
     * List available models
     */
    public function listModels(): array 
    {
        try {
            $response = $this->connector->send(
                new \Saloon\Http\Request(
                    method: \Saloon\Enums\Method::GET,
                    endpoint: '/api/tags'
                )
            );

            return $response->json('models', []);
        } catch (\Exception $e) {
            Log::error('Failed to list Ollama models', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
