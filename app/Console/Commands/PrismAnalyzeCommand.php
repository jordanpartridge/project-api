<?php

namespace App\Console\Commands;

use App\Services\CodeAnalysis\StreamingAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * AI-powered code analysis command using Prism.
 *
 * @property string $signature prism:analyze {path?} Path to analyze, defaults to app directory
 * @property string $description Command to perform AI-powered code analysis
 */
class PrismAnalyzeCommand extends Command
{
    protected $signature = 'prism:analyze {path?}';
    protected $description = 'Perform AI-powered code analysis using Prism';

    public function handle(StreamingAnalyzer $analyzer)
    {
        $path = $this->argument('path') ?? app_path();

        if (! File::exists($path)) {
            $this->error("Path does not exist: {$path}");

            return Command::FAILURE;
        }

        $this->info('🔍 Code Analysis');

        $files = collect(File::files($path))
            ->filter(function ($f) {
                $realPath = $f->getRealPath();

                return pathinfo($realPath, PATHINFO_EXTENSION) === 'php'
                    && ! str_contains($realPath, '/vendor/');
            });

        $progress = $this->output->createProgressBar($files->count());
        $progress->start();

        $criticalIssues = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $progress->setMessage("Analyzing {$filename}");

            $code = File::get($file);
            $fileCriticalIssues = $analyzer->analyzeStream($code);

            if (! empty($fileCriticalIssues)) {
                $criticalIssues[$filename] = $fileCriticalIssues;
            }

            $progress->advance();
        }

        $progress->finish();
        $this->newLine(2);

        if (! empty($criticalIssues)) {
            $this->warn('Critical Issues Found:');
            foreach ($criticalIssues as $file => $issues) {
                $this->info("📄 {$file}:");
                foreach ($issues as $issue) {
                    $this->warn(" - {$issue}");
                }
            }

            return Command::FAILURE;
        }

        $this->info('✅ No critical issues found.');

        return Command::SUCCESS;
    }
}
