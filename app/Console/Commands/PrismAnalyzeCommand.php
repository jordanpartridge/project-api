<?php

namespace App\Console\Commands;

use EchoLabs\Prism\Facades\Prism;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PrismAnalyzeCommand extends Command
{
    protected $signature = 'prism:analyze {path?} {--provider=anthropic}';
    protected $description = 'AI-powered code analysis using Prism';

    public function handle()
    {
        $path = $this->argument('path') ?? app_path();
        $provider = $this->option('provider');

        $this->info("Analyzing {$path} using {$provider}...");

        $files = File::files($path);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $code = File::get($file);
            $response = Prism::text()->anthropic()
                ->withPrompt("Analyze this PHP code:\n{$code}")
                ->stream();

            $this->info("\nAnalyzing " . basename($file) . ':');

            foreach ($response as $chunk) {
                $this->line($chunk);
            }
        }

        $this->info("\nAnalysis complete.");

        return 0;
    }
}
