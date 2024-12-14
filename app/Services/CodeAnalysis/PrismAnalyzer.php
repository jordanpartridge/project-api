<?php

namespace App\Services\CodeAnalysis;

use EchoLabs\Prism\Prism;
use Exception;

class PrismAnalyzer
{
    public function analyze($code, $filename): array
    {
        $prompt = <<<EOT
Analyze this code ({$filename}) and return a JSON object with:
{
    "critical": [{  // Max 2 highest priority fixes needed
        "description": "50 char max description",
        "solution": "50 char max solution"
    }],
    "opportunities": [{  // Top 3 improvement opportunities
        "type": "security|performance|design",
        "description": "50 char max"
    }]
}

Code:
{$code}
EOT;

        try {
            $prism = new Prism;
            $response = $prism->text()
                ->using('anthropic', 'claude-3-opus-20240229')
                ->withPrompt($prompt)
                ->generate();

            return json_decode($response->text, true) ?: $this->emptyResponse();
        } catch (Exception $e) {
            return $this->emptyResponse();
        }
    }

    private function emptyResponse(): array
    {
        return [
            'critical' => [],
            'opportunities' => [],
        ];
    }
}
