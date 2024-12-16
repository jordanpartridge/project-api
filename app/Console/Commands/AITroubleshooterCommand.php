<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Input\InputOption;

class AITroubleshooterCommand extends Command
{
    protected $signature = 'ai:troubleshoot 
        {input? : The input text to analyze}
        {--f|file= : Path to a file containing the failure details}
        {--s|service=claude : Which AI service to use (claude, groq, openai)}
        {--quiet : Run in quiet mode}';

    protected $description = 'Use AI to help troubleshoot technical problems';

    private const SERVICES = [
        'claude' => 'makeAnthropicRequest',
        'groq' => 'makeGroqRequest',
        'openai' => 'makeOpenAIRequest'
    ];

    private const SYSTEM_PROMPT = <<<'PROMPT'
You are an expert software engineer and debugging assistant. 
Analyze the technical problem with the following structure:

## Diagnosis
[Precise problem identification]

## Root Causes
[Potential underlying reasons]

## Solution Strategies
[Recommended fixes and approaches]

## Code Recommendations
[Specific code-level suggestions]

## Prevention Tips
[Best practices to avoid similar issues]

Be specific, technical, and actionable in your response.
PROMPT;

    public function handle()
    {
        $input = $this->input();
        $service = $this->option('service');

        try {
            $analysisMethod = self::SERVICES[$service] ?? 'makeAnthropicRequest';
            $response = $this->{$analysisMethod}($input);
            $this->outputResponse($response);
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Analysis failed: " . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function input(): string
    {
        // Prioritize input sources in this order:
        // 1. Directly provided argument
        // 2. File input
        // 3. Piped stdin
        if ($this->argument('input')) {
            return $this->argument('input');
        }

        if ($this->option('file')) {
            return file_get_contents($this->option('file'));
        }

        // Read from stdin
        $stdin = fopen('php://stdin', 'r');
        $input = stream_get_contents($stdin);
        fclose($stdin);

        return trim($input);
    }

    private function makeAnthropicRequest(string $query): string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-key' => env('ANTHROPIC_API_KEY'),
            'anthropic-version' => '2023-06-01'
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-3-opus-20240229',
            'max_tokens' => 4000,
            'messages' => [
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                ['role' => 'user', 'content' => $query]
            ]
        ]);

        return $response->json('content.0.text', 'No response');
    }

    private function makeGroqRequest(string $query): string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('GROQ_API_KEY')
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama3-70b-8192',
            'messages' => [
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                ['role' => 'user', 'content' => $query]
            ]
        ]);

        return $response->json('choices.0.message.content', 'No response');
    }

    private function makeOpenAIRequest(string $query): string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY')
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4-turbo',
            'messages' => [
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                ['role' => 'user', 'content' => $query]
            ]
        ]);

        return $response->json('choices.0.message.content', 'No response');
    }

    private function outputResponse(string $response)
    {
        if ($this->option('quiet')) {
            fwrite(STDOUT, $response . "\n");
        } else {
            $this->output->writeln($response);
        }
    }
}
