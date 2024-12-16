<?php

namespace App\Services\PrivateGPT;

use Illuminate\Support\Facades\Http;

class PrivateGPTService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('privategpt.url', 'http://localhost:8001');
    }

    public function query(string $prompt)
    {
        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/v1/chat/completions", [
                    'model' => config('privategpt.model', 'default'),
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ]
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => 'Failed to get response',
                'status' => $response->status(),
                'body' => $response->body()
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Exception occurred',
                'message' => $e->getMessage()
            ];
        }
    }
}