<?php

namespace App\Console\Commands;

use App\Tool\GetFunctionForFile;
use EchoLabs\Prism\Prism;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;

use function Laravel\Prompts\suggest;

class AnalyzeModel extends Command
{
    protected $signature = 'analyze:model {query?}';

    protected $description = 'Analyze a model, find documentation, and provide recommendations.';

    public function handle(): void
    {
        $query = $this->argument('query') ?? $this->askForQuery();
        $model = $this->findModel($query);

        if ($model) {
            $this->info("Found model: {$model}");
            $this->analyzeModelRelationships($model);
        } else {
            $this->error('Model not found.');
        }
    }

    private function askForQuery(): string
    {
        return suggest('Enter a model or query', $this->getAllModelNames());
    }

    private function findModel(string $query): ?string
    {
        $models = $this->getAllModelNames();

        foreach ($models as $model) {
            $levenshteinDistance = levenshtein(strtolower($query), strtolower($model));
            $modelLength = strlen($model);
            $matchPercentage = (1 - ($levenshteinDistance / $modelLength)) * 100;

            if ($matchPercentage >= 50) {
                return $model;
            }
        }

        return null;
    }

    private function getAllModelNames(): array
    {
        $modelsPath = app_path('Models');
        $files = File::allFiles($modelsPath);
        $modelNames = [];

        foreach ($files as $file) {
            $modelNames[] = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }

        return $modelNames;
    }

    private function analyzeModelRelationships(string $model): void
    {
        $tool = new GetFunctionForFile;

        $prompt = "Starting analysis of {$model} model...";
        $this->info($prompt);

        $response = Prism::text()
            ->using('anthropic', 'claude-3-5-sonnet-20241022')
            ->withTools([$tool])
            ->withPrompt($prompt)
            ->generate();

        [
            'toolResults' => $toolResults,
            'toolCalls' => $toolCalls,
            'text' => $text,
        ] = (array) $response;

        activity()
            ->withProperties(['tool-results' => $toolResults, 'tool-calls' => $toolCalls])
            ->log('tool-run');

        $this->info('Text Output:');
        $this->line($text);

        $response = Prism::text()
            ->using('anthropic', 'claude-3-5-sonnet-20241022')
            ->withPrompt(sprintf(
                'Analyze tool results and provide insights: %s',
                json_encode($toolResults)
            ))->generate();

        if (! $response) {
            throw new RuntimeException('No response received from analysis');
        }

        $this->info("Analysis for {$model}:");
        $this->newLine();

        // Use Laravel's built-in output formatting
        $this->line($response->text);
    }
}
