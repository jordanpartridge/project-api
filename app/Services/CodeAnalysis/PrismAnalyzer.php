<?php

namespace App\Services\CodeAnalysis;

use EchoLabs\Prism\Prism;

class PrismAnalyzer
{
    public function analyze($code): array
    {
        $prompt = <<<EOT
Analyze this code and return a JSON object with:
{
    "issues": [{
        "type": "security|performance|design",
        "severity": 1-5,
        "line": line_number,
        "description": "Brief issue description",
        "solution": "Brief solution"
    }],
    "good_practices": [{
        "type": "security|performance|design",
        "description": "Brief description"
    }]
}
Ensure output is valid JSON. Focus on critical issues (severity 4-5).

Code:
{$code}
EOT;

        $prism = new Prism;
        $response = $prism->text()
            ->using('anthropic', 'claude-3-opus-20240229')
            ->withPrompt($prompt)
            ->generate();

        return json_decode($response->text, true) ?: [
            'issues' => [],
            'good_practices' => [],
        ];
    }
}
