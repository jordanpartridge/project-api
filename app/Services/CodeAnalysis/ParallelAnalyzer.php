<?php

namespace App\Services\CodeAnalysis;

use EchoLabs\Prism\Prism;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ParallelAnalyzer
{
    protected $timeout = 30;
    protected $cacheTime = 1440; // 24 hours

    public function analyzeFiles(Collection $files, string $provider, string $model): Collection
    {
        return $files->map(function ($file) use ($provider, $model) {
            return $this->analyzeFile($file, $provider, $model);
        });
    }

    protected function analyzeFile($file, $provider, $model)
    {
        $hash = md5(File::get($file));
        $cacheKey = "code_analysis_{$file}_{$hash}";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($file, $provider, $model) {
            try {
                $prism = new Prism;
                $code = File::get($file);

                $response = $prism->text()
                    ->using($provider, $model)
                    ->withTimeout($this->timeout)
                    ->withPrompt($this->buildPrompt($code))
                    ->generate();

                return $this->parseResponse($response->text);
            } catch (Exception $e) {
                return ['error' => $e->getMessage()];
            }
        });
    }

    protected function buildPrompt($code): string
    {
        return "Review this code and provide:
1. STRENGTHS: List 1-2 good practices found (prefix with '+')
2. ISSUES: List 1-2 critical issues that need attention (prefix with '-')
3. For each issue, provide a one-line solution (prefix with '>')

Keep responses very brief - one line per item.

Code:\n{$code}";
    }

    protected function parseResponse($text): array
    {
        $results = ['strengths' => [], 'issues' => []];

        foreach (explode("\n", $text) as $line) {
            $line = trim($line);
            if (str_starts_with($line, '+')) {
                $results['strengths'][] = trim(substr($line, 1));
            } elseif (str_starts_with($line, '-')) {
                $results['issues'][] = ['issue' => trim(substr($line, 1))];
            } elseif (str_starts_with($line, '>')) {
                if (! empty($results['issues'])) {
                    $results['issues'][count($results['issues']) - 1]['solution'] = trim(substr($line, 1));
                }
            }
        }

        return $results;
    }
}
