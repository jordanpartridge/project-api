<?php

namespace JordanPartridge\GithubClient\Requests\GraphQL;

use Saloon\Contracts\Response;
use Saloon\GraphQL\Request;

abstract class AbstractGraphQLRequest extends Request
{
    /**
     * Provides a standardized way to transform GraphQL responses
     * 
     * @param Response $response
     * @return mixed
     */
    public function transformResponse(Response $response): mixed
    {
        $data = $response->json();

        // Basic error handling
        if (isset($data['errors'])) {
            throw new \Exception('GraphQL Query Error: ' . json_encode($data['errors']));
        }

        return $this->parseData($data['data'] ?? []);
    }

    /**
     * Custom parsing method to be implemented by specific request types
     * 
     * @param array $data
     * @return mixed
     */
    abstract protected function parseData(array $data): mixed;

    /**
     * Provide a default set of variables for the query
     * 
     * @return array
     */
    public function defaultVariables(): array
    {
        return [];
    }
}
