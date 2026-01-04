<?php

namespace App\Services\AI\Models;

use Illuminate\Support\Facades\Http;
use App\Services\AI\Exceptions\AIModelException;

class OllamaConnector implements AIModelInterface
{
    protected string $endpoint;
    protected array $models;
    
    public function __construct(string $endpoint, array $models)
    {
        $this->endpoint = $endpoint;
        $this->models = $models;
    }
    
    public function analyze(string $code, array $context = []): array
    {
        $response = Http::post("{$this->endpoint}/api/analyze", [
            'code' => $code,
            'context' => $context,
            'model' => $this->selectModel('analysis')
        ]);
        
        if (!$response->successful()) {
            throw new AIModelException("Ollama analysis failed: " . $response->body());
        }
        
        return $response->json();
    }
    
    public function suggest(string $context, array $options = []): array
    {
        $response = Http::post("{$this->endpoint}/api/generate", [
            'prompt' => $context,
            'options' => $options,
            'model' => $this->selectModel('default')
        ]);
        
        if (!$response->successful()) {
            throw new AIModelException("Ollama suggestion failed: " . $response->body());
        }
        
        return $response->json();
    }
    
    public function review(string $code, array $standards = []): array
    {
        $response = Http::post("{$this->endpoint}/api/review", [
            'code' => $code,
            'standards' => $standards,
            'model' => $this->selectModel('review')
        ]);
        
        if (!$response->successful()) {
            throw new AIModelException("Ollama review failed: " . $response->body());
        }
        
        return $response->json();
    }
    
    public function explain(string $code, string $format = 'markdown'): string
    {
        $response = Http::post("{$this->endpoint}/api/explain", [
            'code' => $code,
            'format' => $format,
            'model' => $this->selectModel('default')
        ]);
        
        if (!$response->successful()) {
            throw new AIModelException("Ollama explanation failed: " . $response->body());
        }
        
        return $response->json()['explanation'];
    }
    
    public function optimize(string $code, array $constraints = []): string
    {
        $response = Http::post("{$this->endpoint}/api/optimize", [
            'code' => $code,
            'constraints' => $constraints,
            'model' => $this->selectModel('default')
        ]);
        
        if (!$response->successful()) {
            throw new AIModelException("Ollama optimization failed: " . $response->body());
        }
        
        return $response->json()['optimized_code'];
    }
    
    protected function selectModel(string $task): string
    {
        return $this->models[$task] ?? $this->models['default'];
    }
}