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

        $files = File::files($path);
        $issues = [];
        $strengths = [];

        $this->components->info('Starting code analysis...');

        $this->components->task('Scanning files', function () use ($files) {
            return count($files) > 0;
        });

        $this->components->task('Setting up AI', function () {
            return true;
        });

        $this->components->task('Analyzing code', function () use ($files, &$issues, &$strengths, $provider, $model) {
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                    continue;
                }

                $code = File::get($file->getPathname());
                $filename = basename($file);

                try {
                    $prompt = "Review this code and provide:
1. STRENGTHS: List 1-2 good practices found (prefix with '+')
2. ISSUES: List 1-2 critical issues that need attention (prefix with '-')
3. For each issue, provide a one-line solution (prefix with '>')

Keep responses very brief - one line per item.

Code:\n{$code}";

                    $prism = new Prism;
                    $response = $prism->text()
                        ->using($provider, $model)
                        ->withPrompt($prompt)
                        ->generate();

                    if (trim($response->text)) {
                        $fileResults = ['strengths' => [], 'issues' => []];
                        foreach (explode("\n", $response->text) as $line) {
                            if (str_starts_with(trim($line), '+')) {
                                $fileResults['strengths'][] = trim(substr($line, 1));
                            } elseif (str_starts_with(trim($line), '-')) {
                                $fileResults['issues'][] = [
                                    'issue' => trim(substr($line, 1)),
                                ];
                            } elseif (str_starts_with(trim($line), '>')) {
                                if (count($fileResults['issues']) > 0) {
                                    $fileResults['issues'][count($fileResults['issues']) - 1]['solution'] = trim(substr($line, 1));
                                }
                            }
                        }

                        if (! empty($fileResults['strengths']) || ! empty($fileResults['issues'])) {
                            $issues[$filename] = $fileResults['issues'];
                            $strengths[$filename] = $fileResults['strengths'];
                        }
                    }
                } catch (Exception $e) {
                    $this->components->error($filename . ': ' . $e->getMessage());

                    return false;
                }
            }

            return true;
        });

        $this->newLine();
        $this->components->info('Analysis Results:');

        foreach ($strengths as $file => $fileStrengths) {
            $this->newLine();
            $this->line("<fg=white;options=bold>{$file}</>");
            foreach ($fileStrengths as $strength) {
                $this->line("  <fg=green>✓</> {$strength}");
            }
            if (isset($issues[$file])) {
                foreach ($issues[$file] as $issue) {
                    $this->line("  <fg=red>✗</> {$issue['issue']}");
                    $this->line("    <fg=yellow>→</> {$issue['solution']}");
                }
            }
        }

        return 0;
    }
}
