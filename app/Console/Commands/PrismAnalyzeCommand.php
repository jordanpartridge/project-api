<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PrismAnalyzeCommand extends Command
{
    protected $signature = 'prism:analyze {path?} {--provider=anthropic}';
    protected $description = 'AI-powered code analysis using Prism';

    public function handle()
    {
        $path = $this->argument('path') ?? app_path();
        $provider = $this->option('provider');

        $this->info("Analyzing {$path} using {$provider}...");

        // Your analysis logic here
        $this->info('Analysis complete.');

        return 0;
    }
}
