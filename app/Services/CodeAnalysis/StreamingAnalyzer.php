<?php

namespace App\Services\CodeAnalysis;

use EchoLabs\Prism\Prism;
use Exception;
use Generator;

class StreamingAnalyzer
{
    protected array $focusAreas = ['security', 'performance', 'design'];

    public function analyzeStream($code, $filename): Generator
    {
        foreach ($this->focusAreas as $area) {
            $prompt = $this->buildPrompt($code, $area);

            try {
                $prism = new Prism;
                $response = $prism->text()
                    ->using('anthropic', 'claude-3-opus-20240229')
                    ->withPrompt($prompt)
                    ->generate();

                yield [
                    'area' => $area,
                    'result' => json_decode($response->text, true) ?? [],
                ];
            } catch (Exception $e) {
                yield [
                    'area' => $area,
                    'error' => $e->getMessage(),
                ];
            }
        }
    }

    protected function buildPrompt($code, $area): string
    {
        return <<<EOT
Analyze ONLY {$area} issues in this PHP code. Return JSON:
{
    "issues": [{
        "severity": 1-5,
        "description": "50 char max",
        "solution": "50 char max"
    }]
}
Keep responses focused and precise.

Code:
{$code}
EOT;
    }
}
