<?php

namespace App\Services\CodeAnalysis;

use EchoLabs\Prism\Prism;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ParallelAnalyzer
{
    protected int $cacheTime;

    public function __construct()
    {
        $this->cacheTime = now()->addDays(7);
    }

    public function analyzeFiles(array $files, string $provider, string $model): array
    {
        return collect($files)
            ->mapWithKeys(fn ($file) => [$file => $this->analyzeFile($file, $provider, $model)])
            ->filter()
            ->toArray();
    }

    protected function analyzeFile(string $file, string $provider, string $model): array
    {
        $code = File::get($file);
        $hash = md5($code);
        $cacheKey = "code_analysis_{$file}_{$hash}";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($file, $provider, $model, $code) {
            try {
                $prism = new Prism;

                $response = $prism->text()
                    ->using($provider, $model)
                    ->withPrompt($this->buildPrompt($code))
                    ->generate();

                return [
                    'file' => $file,
                    'response' => $response,
                ];
            } catch (Exception $e) {
                logger()->error("Code analysis failed for {$file}: {$e->getMessage()}");

                return [];
            }
        });
    }

    protected function buildPrompt(string $code): string
    {
        // Existing prompt building logic
        return "Analyze the following code and provide insights:\n\n{$code}";
    }
}
