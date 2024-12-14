<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;

class AnalyzeModel extends Command
{
    protected $signature = 'model:analyze {model?}';
    protected $description = 'Analyze Eloquent models and their relationships';

    public function handle()
    {
        $model = $this->argument('model');

        if (! $model) {
            $model = $this->askForModel();
        }

        $fqn = $this->getFullyQualifiedClassName($model);

        if (! class_exists($fqn)) {
            warning("Model {$model} not found.");

            return Command::FAILURE;
        }

        $this->analyzeModelRelationships($fqn);

        return Command::SUCCESS;
    }

    private function askForModel(): string
    {
        $models = $this->getAllModelNames();

        return suggest(
            'Which model would you like to analyze?',
            $models
        );
    }

    private function getFullyQualifiedClassName(string $model): string
    {
        $modelNamespace = 'App\\Models\\';

        return Str::startsWith($model, $modelNamespace)
            ? $model
            : $modelNamespace . $model;
    }

    private function getAllModelNames(): array
    {
        $modelPath = app_path('Models');

        return collect(File::files($modelPath))
            ->map(fn ($file) => pathinfo($file, PATHINFO_FILENAME))
            ->toArray();
    }

    private function analyzeModelRelationships(string $modelClass)
    {
        $reflectionClass = new ReflectionClass($modelClass);
        $model = new $modelClass;

        info("Analyzing model: {$modelClass}");

        $methods = collect($reflectionClass->getMethods())
            ->filter(function ($method) {
                return $method->isPublic() &&
                       ! $method->isStatic() &&
                       Str::startsWith($method->getName(), ['belongsTo', 'hasMany', 'hasOne', 'belongsToMany']);
            });

        if ($methods->isEmpty()) {
            warning("No relationships found in {$modelClass}.");

            return;
        }

        info('Relationships:');
        $methods->each(function ($method) use ($model) {
            try {
                $relationshipInstance = $method->invoke($model);
                $relationType = class_basename($relationshipInstance);

                info("- {$method->getName()}: {$relationType} relationship");
            } catch (Exception $e) {
                warning("Could not analyze {$method->getName()}: {$e->getMessage()}");
            }
        });
    }
}
