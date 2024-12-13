<?php

namespace JordanPartridge\GithubClient\Connectors;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Contracts\Authenticator;
use Saloon\Authenticators\BearerTokenAuthenticator;

class GraphQLConnector extends Connector
{
    use AcceptsJson;

    protected ?string $token;

    public function __construct(?string $token = null)
    {
        $this->token = $token ?? config('github-client.token');
    }

    public function resolveBaseUrl(): string
    {
        return 'https://api.github.com/graphql';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected function defaultAuthentication(): ?Authenticator
    {
        return new BearerTokenAuthenticator($this->token);
    }

    /**
     * Intelligent method to handle different types of GraphQL requests
     * 
     * @param mixed $request
     * @return mixed
     */
    public function send(mixed $request): mixed
    {
        try {
            $response = parent::send($request);
            return $request->transformResponse($response);
        } catch (\Exception $e) {
            // Advanced error handling
            \Log::error('GraphQL Request Failed', [
                'message' => $e->getMessage(),
                'request' => $request->definition(),
                'variables' => $request->variables()
            ]);

            throw $e;
        }
    }
}
