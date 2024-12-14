<?php

namespace App\Console\Commands;

use App\Services\CodeAnalysis\ParallelAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class PrismAnalyzeCommand extends Command
{
    protected $signature = 'prism:analyze
        {path? : Path to analyze}
        {--provider=anthropic : AI provider to use}
        {--model=claude-3-opus-20240229 : Model to use}
        {--focus=all : Focus areas (security,performance,design)}
        {--depth=normal : Analysis depth (quick,normal,deep)}';

    protected $description = 'AI-powered code analysis using Prism';

    public function handle(ParallelAnalyzer $analyzer)
    {
        $path = $this->argument('path') ?? app_path();
        $provider = $this->option('provider');
        $model = $this->option('model');

        if (! is_dir($path)) {
            $this->components->error("Directory not found: {$path}");

            return 1;
        }

        $files = Collection::make(File::files($path))
            ->filter(fn ($file) => pathinfo($file, PATHINFO_EXTENSION) === 'php');

        if ($files->isEmpty()) {
            $this->components->error("No PHP files found in {$path}");

            return 1;
        }

        $this->components->info('Starting code analysis...');

        $this->components->task('Scanning files', fn () => $files->count() > 0);
        $this->components->task('Setting up AI', fn () => true);

        $results = [];
        $errors = [];

        $this->components->task('Analyzing code', function () use ($analyzer, $files, $provider, $model, &$results, &$errors) {
            $analyses = $analyzer->analyzeFiles($files, $provider, $model);

            foreach ($analyses as $path => $analysis) {
                $filename = basename($path);
                if (isset($analysis['error'])) {
                    $errors[$filename] = $analysis['error'];
                } else {
                    $results[$filename] = $analysis;
                }
            }

            return empty($errors);
        });

        $this->newLine();
        $this->components->info('Analysis Results:');

        foreach ($results as $file => $result) {
            $this->newLine();
            $this->line("<fg=white;options=bold>{$file}</>");

            foreach ($result['strengths'] as $strength) {
                $this->line("  <fg=green>✓</> {$strength}");
            }

            foreach ($result['issues'] as $issue) {
                $this->line("  <fg=red>✗</> {$issue['issue']}");
                if (isset($issue['solution'])) {
                    $this->line("    <fg=yellow>→</> {$issue['solution']}");
                }
            }
        }

        if (! empty($errors)) {
            $this->newLine();
            $this->components->warn('Errors:');
            foreach ($errors as $file => $error) {
                $this->line("  <fg=red>✗</> {$file}: {$error}");
            }
        }

        return empty($errors) ? 0 : 1;
    }
}
