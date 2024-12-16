<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Input\InputOption;

class TestAIServicesCommand extends Command
{
    protected $signature = 'ai:test-services {query}';
    protected $description = 'Test queries across different AI services';

    protected function configure()
    {
//        $this->addOption(
//            'no-interaction',
//            null,
//            InputOption::VALUE_NONE,
//            'Run in non-interactive mode'
//        );
    }

    public function handle()
    {
        $query = $this->argument('query');
        $noInteraction = $this->option('no-interaction');

        try {
            $apiKey = env('ANTHROPIC_API_KEY');
            $this->outputResponse('Anthropic Claude', $this->makeAnthropicRequest($query, $apiKey), $noInteraction);

            $apiKey = env('GROQ_API_KEY');
            $this->outputResponse('Groq', $this->makeGroqRequest($query, $apiKey), $noInteraction);

            $apiKey = env('OPENAI_API_KEY');
            $this->outputResponse('OpenAI', $this->makeOpenAIRequest($query, $apiKey), $noInteraction);

            return self::SUCCESS;
        } catch (\Exception $e) {
            if ($noInteraction) {
                fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
            } else {
                $this->error($e->getMessage());
            }
            return self::FAILURE;
        }
    }

    private function outputResponse(string $serviceName, string $response, bool $noInteraction)
    {
        if ($noInteraction) {
            fwrite(STDOUT, "$serviceName: $response\n");
        } else {
            $this->info("$serviceName Query:");
            $this->line($response);
        }
    }

    private function makeAnthropicRequest(string $query, string $apiKey): string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01'
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-3-opus-20240229',
            'max_tokens' => 1000,
            'messages' => [
                ['role' => 'user', 'content' => $query]
            ]
        ]);

        return $response->json('content.0.text', 'No response');
    }

    private function makeGroqRequest(string $query, string $apiKey): string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama3-70b-8192',
            'messages' => [
                ['role' => 'user', 'content' => $query]
            ]
        ]);

        return $response->json('choices.0.message.content', 'No response');
    }

    private function makeOpenAIRequest(string $query, string $apiKey): string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $query]
            ]
        ]);

        return $response->json('choices.0.message.content', 'No response');
    }
}
