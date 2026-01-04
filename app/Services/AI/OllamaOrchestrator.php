<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OllamaOrchestrator
{
    protected string $endpoint;
    protected array $models = [
        'code' => 'codellama',
        'analysis' => 'codellama',
        'review' => 'codellama'
    ];

    private array $contextPrompts = [
        'analysis' => "You are an expert Laravel developer analyzing code. Focus on:
1. Design patterns
2. Performance implications
3. Security concerns
4. Best practices
5. Potential edge cases",
        
        'generation' => "You are an expert Laravel developer generating code. Follow these rules:
1. Use Laravel best practices and design patterns
2. Include PHPDoc comments and type hints
3. Follow PSR-12 standards
4. Implement proper error handling
5. Consider security implications
6. Add relevant tests",
        
        'review' => "You are a senior code reviewer. Evaluate this code for:
1. Logical errors
2. Security vulnerabilities
3. Performance issues
4. Design pattern adherence
5. Test coverage
6. Documentation completeness"
    ];

    public function __construct()
    {
        $this->endpoint = config('ai.ollama.endpoint', 'http://localhost:11434');
    }

    public function generateCode(string $task, array $context = []): array
    {
        $prompt = $this->buildPrompt('generation', $task, $context);
        
        try {
            $response = $this->callOllama($prompt, 'code');
            
            // Validate the generated code
            $validationResult = $this->validateCode($response['response']);
            
            if (!$validationResult['isValid']) {
                // Try one more time with more specific constraints
                $refinedPrompt = $this->refinePrompt($prompt, $validationResult['issues']);
                $response = $this->callOllama($refinedPrompt, 'code');
            }
            
            return [
                'code' => $response['response'],
                'validation' => $validationResult,
                'context' => $context
            ];
        } catch (\Exception $e) {
            Log::error('Code generation failed', [
                'error' => $e->getMessage(),
                'task' => $task,
                'context' => $context
            ]);
            
            throw new \RuntimeException('Failed to generate code: ' . $e->getMessage());
        }
    }

    public function analyzeCode(string $code): array
    {
        $prompt = $this->buildPrompt('analysis', $code);
        
        try {
            $response = $this->callOllama($prompt, 'analysis');
            return [
                'analysis' => $response['response'],
                'timestamp' => now()
            ];
        } catch (\Exception $e) {
            Log::error('Code analysis failed', [
                'error' => $e->getMessage(),
                'code_hash' => md5($code)
            ]);
            
            throw new \RuntimeException('Failed to analyze code: ' . $e->getMessage());
        }
    }

    public function reviewCode(string $code, array $standards = []): array
    {
        $prompt = $this->buildPrompt('review', $code, $standards);
        
        try {
            $response = $this->callOllama($prompt, 'review');
            return [
                'review' => $response['response'],
                'standards_checked' => $standards,
                'timestamp' => now()
            ];
        } catch (\Exception $e) {
            Log::error('Code review failed', [
                'error' => $e->getMessage(),
                'code_hash' => md5($code)
            ]);
            
            throw new \RuntimeException('Failed to review code: ' . $e->getMessage());
        }
    }

    protected function buildPrompt(string $type, string $content, array $context = []): string
    {
        $basePrompt = $this->contextPrompts[$type] ?? '';
        
        $prompt = "{$basePrompt}\n\n";
        $prompt .= "Context:\n";
        
        foreach ($context as $key => $value) {
            $prompt .= "- {$key}: {$value}\n";
        }
        
        $prompt .= "\nTask/Content:\n{$content}";
        
        return $prompt;
    }

    protected function refinePrompt(string $originalPrompt, array $issues): string
    {
        $refinements = "Previous generation had these issues:\n";
        foreach ($issues as $issue) {
            $refinements .= "- {$issue}\n";
        }
        
        return $originalPrompt . "\n\n" . $refinements . "\nPlease address these issues in the new generation.";
    }

    protected function validateCode(string $code): array
    {
        $issues = [];
        
        // Basic syntax check
        if (strpos($code, '<?php') === false) {
            $issues[] = 'Missing PHP opening tag';
        }
        
        // Check for Laravel patterns
        if (strpos($code, 'namespace App') === false) {
            $issues[] = 'Missing or incorrect namespace';
        }
        
        // Check for type hints
        if (!preg_match('/function\s+\w+\([^)]*\)\s*:\s*\w+/', $code)) {
            $issues[] = 'Missing return type hints';
        }
        
        // Check for PHPDoc
        if (!preg_match('/\/\*\*[\s\S]*?\*\//', $code)) {
            $issues[] = 'Missing PHPDoc comments';
        }
        
        return [
            'isValid' => empty($issues),
            'issues' => $issues
        ];
    }

    protected function callOllama(string $prompt, string $modelType): array
    {
        $cacheKey = 'ollama:' . md5($prompt . $modelType);
        
        return Cache::remember($cacheKey, 3600, function () use ($prompt, $modelType) {
            $response = Http::post("{$this->endpoint}/api/generate", [
                'model' => $this->models[$modelType],
                'prompt' => $prompt,
                'stream' => false
            ]);
            
            if (!$response->successful()) {
                throw new \RuntimeException('Ollama API call failed: ' . $response->body());
            }
            
            return $response->json();
        });
    }
}