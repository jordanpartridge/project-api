<?php

namespace App\Services\PrivateGPT\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CompletionRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $prompt,
        protected ?array $context = null,
        protected ?array $options = []
    ) {}

    public function resolveEndpoint(): string
    {
        return '/v1/completions';
    }

    protected function defaultBody(): array
    {
        return array_filter([
            'prompt' => $this->prompt,
            'context' => $this->context,
            'options' => $this->options,
        ]);
    }
}
