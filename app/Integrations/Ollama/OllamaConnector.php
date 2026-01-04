<?php

namespace App\Integrations\Ollama;

use Saloon\Http\Connector;
use Saloon\Contracts\Authenticator;
use Saloon\Traits\Plugins\AcceptsJson;

class OllamaConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL for the Ollama API
     */
    public function resolveBaseUrl(): string
    {
        return config('services.ollama.url', 'http://localhost:11434');
    }

    /**
     * Default headers for all requests
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Configure request timeout
     */
    protected function defaultConfig(): array
    {
        return [
            'timeout' => config('services.ollama.timeout', 120),
        ];
    }

    /**
     * Global request middleware
     */
    protected function middleware(): array
    {
        return [
            // Add logging or other middleware as needed
        ];
    }
}
