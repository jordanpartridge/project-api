<?php

namespace App\Console\Commands;

use App\Tool\GetFunctionForFile;
use EchoLabs\Prism\Prism;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\suggest;

class AnalyzeModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analyze:model {query?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze a model, find documentation, and provide recommendations.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $query = $this->argument('query') ?? $this->askForQuery();

        // Search for models and relationships based on query
        $model = $this->findModel($query);

        if ($model) {
            $this->info("Found model: {$model}");
            $this->analyzeModelRelationships($model);
        } else {
            $this->error('Model not found.');
        }
    }

    /**
     * Ask for the query if it's not provided in the arguments.
     */
    private function askForQuery(): string
    {
        // Using Laravel Prompts to ask for input with suggestions
        return suggest('Enter a model or query', $this->getAllModelNames());
    }

    /**
     * Find the model based on fuzzy matching or suggestions.
     */
    private function findModel(string $query): ?string
    {
        $models = $this->getAllModelNames();

        // Check for exact match or fuzzy match (threshold > 50%)
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

    /**
     * Get all model names from app/Models directory.
     */
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

    /**
     * Analyze the model's relationships (like hasMany, belongsTo).
     */
    private function analyzeModelRelationships(string $model): void
    {
        $fqn = $this->getFullyQualifedClassName($model);
        if (! class_exists($fqn)) {
            $this->error("Model {$fqn} does not exist.");

            return;
        }

        $getFunction = new GetFunctionForFile;

        $response = Prism::text()->using('anthropic', 'claude-3-5-sonnet-20241022')
            ->withPrompt("Please analyze the relationships of the model {$fqn}.")
            ->withTools([$getFunction])
            ->generate();

        if (empty($relationships)) {
            $this->info("No relationships found for {$modelNamespace}.");
        } else {
            $this->info("Relationships in {$modelNamespace}:");
            foreach ($relationships as $relationship) {
                $this->info("- {$relationship}");
            }
        }
    }

    /**
     * Get the full namespace of a model.
     */
    private function getFullyQualifedClassName(string $model): string
    {
        return 'App\\Models\\' . ucfirst($model);
    }
}
