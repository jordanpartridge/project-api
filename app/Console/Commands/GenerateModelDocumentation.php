<?php

namespace App\Console\Commands;

use App\Tool\GetFunctionForFile;
use EchoLabs\Prism\Prism;
use Exception;
use Illuminate\Console\Command;

class GenerateModelDocumentation extends Command
{
    protected $signature = 'prism:document-model {model : The model class name to document}';

    protected $description = 'Generate AI-powered documentation for a model using Prism';

    public function handle(): void
    {
        $modelName = $this->argument('model');
        $tool = new GetFunctionForFile;

        try {
            $modelClass = $this->resolveModelClass($modelName);

            if (! $modelClass) {
                $this->error("Model {$modelName} not found!");

                return;
            }

            $this->info("📝 Analyzing model {$modelClass}...");

            // First get the model analysis
            $response = Prism::text()
                ->using('anthropic', 'claude-3-5-sonnet-20241022')
                ->withTools([$tool])
                ->withPrompt("Starting analysis of {$modelName} model for documentation generation...")
                ->generate();

            [
                'toolResults' => $toolResults,
                'toolCalls' => $toolCalls,
                'text' => $text,
            ] = (array) $response;

            // Log tool activity
            activity()
                ->withProperties(['tool-results' => $toolResults, 'tool-calls' => $toolCalls])
                ->log('documentation-analysis');

            // Now generate the documentation
            $response = Prism::text()
                ->using('anthropic', 'claude-3-5-sonnet-20241022')
                ->withPrompt(sprintf(
                    'Generate comprehensive documentation in markdown format for the %s model based on this analysis: %s

                    Include these sections:
                    1. Model Overview
                    2. Database Schema
                    3. Properties & Fields
                    4. Relationships
                    5. Methods
                    6. Security Considerations
                    7. Best Practices
                    8. Code Examples

                    Analysis data: %s',
                    $modelName,
                    $text,
                    json_encode($toolResults)
                ))
                ->generate();

            $this->info('✨ Documentation generated successfully!');
            $this->newLine();

            // Output the markdown documentation
            $this->line($response->text);

            // Optionally save to file
            $docPath = base_path("docs/models/{$modelName}.md");
            if (! file_exists(dirname($docPath))) {
                mkdir(dirname($docPath), 0755, true);
            }
            file_put_contents($docPath, $response->text);
            $this->info("📝 Documentation saved to {$docPath}");

        } catch (Exception $e) {
            $this->error("Error generating documentation: {$e->getMessage()}");

            return;
        }
    }

    protected function resolveModelClass(string $name): ?string
    {
        // Try direct class name first
        if (class_exists($name)) {
            return $name;
        }

        // Try with App\Models namespace
        $modelClass = "App\\Models\\{$name}";
        if (class_exists($modelClass)) {
            return $modelClass;
        }

        return null;
    }
}
