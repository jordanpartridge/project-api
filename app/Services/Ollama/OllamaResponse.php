<?php

namespace App\Services\Ollama;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class OllamaResponse implements Arrayable
{
    public function __construct(
        public readonly string $prompt,
        public readonly string $response,
        public readonly string $model,
        public readonly array $metadata,
        public readonly Carbon $createdAt
    ) {}

    public function toArray(): array
    {
        return [
            'prompt' => $this->prompt,
            'response' => $this->response,
            'model' => $this->model,
            'metadata' => $this->metadata,
            'created_at' => $this->createdAt->toIso8601String(),
        ];
    }
}