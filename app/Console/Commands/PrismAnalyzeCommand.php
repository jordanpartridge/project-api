<?php

namespace App\Console\Commands;

use EchoLabs\Prism\Prism;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PrismAnalyzeCommand extends Command
{
    protected $signature = 'prism:analyze {path?} {--provider=anthropic} {--model=claude-3-opus-20240229}';
    protected $description = 'AI-powered code analysis using Prism';

    public function handle()
    {
        $path = $this->argument('path') ?? app_path();
        $provider = $this->option('provider');
        $model = $this->option('model');

        $this->info("Analyzing {$path}...");

        $files = File::files($path);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $code = File::get($file->getPathname());

            try {
                $prompt = "Review this code and list ONLY the most critical issues that need immediate attention. Focus on security risks, major bugs, and significant performance problems. Keep it very brief - max 3 bullet points.

Code:\n{$code}";

                $prism = new Prism;
                $response = $prism->text()
                    ->using($provider, $model)
                    ->withPrompt($prompt)
                    ->generate();

                $filename = basename($file);
                if (trim($response->text)) {
                    $this->info("\n{$filename}:");
                    $this->line($response->text);
                }
            } catch (Exception $e) {
                $this->error(basename($file) . ': ' . $e->getMessage());
            }
        }

        return 0;
    }
}
