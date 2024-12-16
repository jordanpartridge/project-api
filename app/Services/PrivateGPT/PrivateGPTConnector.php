<?php

namespace App\Services\PrivateGPT;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class PrivateGPTConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return config('services.privategpt.url', 'http://localhost:8001');
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
