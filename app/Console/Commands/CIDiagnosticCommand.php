<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Http;

class CIDiagnosticCommand extends Command
{
    protected $signature = 'ci:diagnose 
        {--filter= : Specific test or analysis to run}
        {--group=all : Analysis group (tests, phpstan, security)}
        {--ai-assist : Use AI to analyze results}';

    protected $description = 'Comprehensive CI/CD diagnostic tool with optional AI assistance';

    private $aiServices = [
        'anthropic' => 'analyzeWithClaude',
        'groq' => 'analyzeWithGroq',
        'openai' => 'analyzeWithOpenAI'
    ];

    public function handle()
    {
        $results = $this->runDiagnostics();
        
        if ($this->option('ai-assist')) {
            $this->aiAssist($results);
        }

        return $this->interpretResults($results);
    }

    private function runDiagnostics(): array
    {
        $group = $this->option('group');
        $filter = $this->option('filter');

        $results = [
            'tests' => $group == 'all' || $group == 'tests' 
                ? $this->runTests($filter) 
                : null,
            'phpstan' => $group == 'all' || $group == 'phpstan' 
                ? $this->runPhpStan($filter) 
                : null,
            // Future expansion: security scans, etc.
        ];

        return $results;
    }

    private function runTests(?string $filter = null): array
    {
        $command = $filter 
            ? "php artisan test --filter='$filter'"
            : 'php artisan test';

        $process = Process::run($command);

        return [
            'success' => $process->successful(),
            'output' => $process->output(),
            'error' => $process->errorOutput()
        ];
    }

    private function runPhpStan(?string $filter = null): array
    {
        // Increase memory limit and add custom configuration
        $command = 'php -d memory_limit=512M ./vendor/bin/phpstan analyse app tests --level=5 --memory-limit=512M';

        $process = Process::run($command);

        return [
            'success' => $process->successful(),
            'output' => $process->output(),
            'error' => $process->errorOutput()
        ];
    }

    private function aiAssist(array $results)
    {
        $combinedOutput = $this->combineOutputs($results);
        $aiService = $this->selectAIService();
        
        $recommendation = $this->{$aiService}($combinedOutput);
        $this->displayRecommendation($recommendation);
    }

    private function combineOutputs(array $results): string
    {
        return collect($results)
            ->filter()
            ->map(fn($result) => $result['output'] . "\n" . $result['error'])
            ->implode("\n---\n");
    }

    private function selectAIService(): string
    {
        // Add intelligent service selection logic
        return 'analyzeWithClaude';
    }

    private function analyzeWithClaude(string $output): string
    {
        // Claude analysis implementation
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-key' => env('ANTHROPIC_API_KEY'),
            'anthropic-version' => '2023-06-01'
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-3-opus-20240229',
            'max_tokens' => 4000,
            'messages' => [
                [
                    'role' => 'user', 
                    'content' => "Analyze these CI/CD diagnostic results. Provide: 1) Summary of issues 2) Specific fix recommendations 3) Potential root causes:\n\n$output"
                ]
            ]
        ]);

        return $response->json('content.0.text', 'No AI analysis available');
    }

    private function interpretResults(array $results): int
    {
        $hasErrors = collect($results)
            ->filter()
            ->contains(fn($result) => !$result['success']);

        return $hasErrors ? self::FAILURE : self::SUCCESS;
    }

    private function displayRecommendation(string $recommendation)
    {
        $this->info("AI Diagnostic Insights:");
        $this->line($recommendation);
    }
}
