<?php

namespace JordanPartridge\GithubClient\Requests\GraphQL;

use Saloon\Contracts\Response;
use Saloon\GraphQL\Request;

abstract class AbstractGraphQLRequest extends Request
{
    /**
     * Provides a standardized way to transform GraphQL responses
     */
    public function transformResponse(Response $response): mixed
    {
        $data = $response->json();

        // Basic error handling
        if (isset($data['errors'])) {
            throw new \Exception('GraphQL Query Error: '.json_encode($data['errors']));
        }

        return $this->parseData($data['data'] ?? []);
    }

    /**
     * Custom parsing method to be implemented by specific request types
     */
    abstract protected function parseData(array $data): mixed;

    /**
     * Provide a default set of variables for the query
     */
    public function defaultVariables(): array
    {
        return [];
    }
}
