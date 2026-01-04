<?php

namespace App\Integrations\Ollama\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Response;
use Illuminate\Support\Facades\Log;

class GenerateTextRequest extends Request
{
    protected Method $method = Method::POST;

    public function __construct(
        private string $model,
        private string $prompt,
        private array $options = []
    ) {}

    /**
     * Resolve the API endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/api/generate';
    }

    /**
     * Prepare the request body
     */
    protected function defaultBody(): array
    {
        return array_merge([
            'model' => $this->model,
            'prompt' => $this->prompt,
            'stream' => false,
            'options' => [
                'temperature' => 0.7,
                'top_p' => 0.9,
            ]
        ], $this->options);
    }

    /**
     * Handle successful response
     */
    public function createResponse(Response $response): array
    {
        // Log generation details
        Log::info('Ollama Text Generation', [
            'model' => $this->model,
            'prompt_length' => strlen($this->prompt),
            'response_length' => strlen($response->json('response') ?? '')
        ]);

        return $response->json();
    }
}
