
<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AIAssistantService
{
    protected $anthropicClient;
    protected $groqClient;
    protected $openaiClient;

    public function __construct()
    {
        $this->initializeClients();
    }

    private function initializeClients()
    {
        // Initialize clients with API keys from environment
        $this->anthropicClient = new Client([
            'base_uri' => 'https://api.anthropic.com/v1/',
            'headers' => [
                'x-api-key' => env('ANTHROPIC_API_KEY'),
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01'
            ]
        ]);

        $this->groqClient = new Client([
            'base_uri' => 'https://api.groq.com/openai/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->openaiClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function askAboutPrivateGPT(string $query)
    {
        // Validate API keys
        if (!$this->validateApiKeys()) {
            throw new \Exception('Missing required API keys');
        }

        // Determine best service for PrivateGPT query
        $serviceResponse = $this->selectAndQueryService($query);

        return $serviceResponse;
    }

    private function validateApiKeys(): bool
    {
        return 
            env('ANTHROPIC_API_KEY') !== null &&
            env('GROQ_API_KEY') !== null &&
            env('OPENAI_API_KEY') !== null;
    }

    private function selectAndQueryService(string $query)
    {
        // Intelligent service selection logic
        $complexity = $this->assessQueryComplexity($query);

        try {
            switch ($complexity) {
                case 'high':
                    return $this->queryAnthropicClaude($query);
                case 'medium':
                    return $this->queryGroq($query);
                default:
                    return $this->queryOpenAI($query);
            }
        } catch (\Exception $e) {
            Log::error('AI Query Error: ' . $e->getMessage());
            return [
                'error' => 'Failed to get AI response',
                'details' => $e->getMessage()
            ];
        }
    }

    private function assessQueryComplexity(string $query): string
    {
        // Basic complexity assessment
        $keywordMap = [
            'high' => ['architecture', 'implementation', 'complex', 'advanced'],
            'medium' => ['configuration', 'setup', 'integration'],
        ];

        foreach ($keywordMap as $complexity => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($query, $keyword) !== false) {
                    return $complexity;
                }
            }
        }

        return 'low';
    }

    private function queryAnthropicClaude(string $query)
    {
        $response = $this->anthropicClient->post('messages', [
            'json' => [
                'model' => 'claude-3-opus-20240229',
                'max_tokens' => 1000,
                'messages' => [
                    ['role' => 'user', 'content' => $query]
                ]
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    private function queryGroq(string $query)
    {
        $response = $this->groqClient->post('chat/completions', [
            'json' => [
                'model' => 'llama3-70b-8192',
                'messages' => [
                    ['role' => 'user', 'content' => $query]
                ]
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    private function queryOpenAI(string $query)
    {
        $response = $this->openaiClient->post('chat/completions', [
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $query]
                ]
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
