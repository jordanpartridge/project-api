<?php

namespace App\Console\Commands;

use EchoLabs\Prism\Facades\PrismAI;
use Exception;
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

            $code = File::get($file->getPathname());

            try {
                $prompt = "Analyze this PHP code for complexity, potential bugs, and optimization opportunities:\n\n{$code}";

                $response = PrismAI::text()
                    ->provider($provider)
                    ->prompt($prompt)
                    ->get();

                $this->info("\nAnalyzing " . basename($file) . ':');
                $this->line($response);
            } catch (Exception $e) {
                $this->error('Error analyzing ' . basename($file) . ': ' . $e->getMessage());
            }
        }

        $this->info("\nAnalysis complete.");

        return 0;
    }
}
