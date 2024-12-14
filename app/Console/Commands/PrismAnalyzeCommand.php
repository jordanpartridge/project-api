<?php

namespace App\Console\Commands;

use App\Services\CodeAnalysis\PrismAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

class PrismAnalyzeCommand extends Command
{
    protected $signature = 'prism:analyze {path?}';
    protected $description = 'AI-powered code analysis using Prism';

    public function handle(PrismAnalyzer $analyzer)
    {
        $path = $this->argument('path') ?? app_path();
        $issues = collect();

        info('Analyzing code...');

        foreach (File::files($path) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $results = $analyzer->analyze(File::get($file));

            if (! empty($results['issues'])) {
                $issues->push([
                    'file' => basename($file),
                    'issues' => collect($results['issues']),
                ]);
            }
        }

        if ($issues->isEmpty()) {
            info('No critical issues found.');

            return 0;
        }

        $rows = $issues->flatMap(function ($item) {
            return collect($item['issues'])->map(function ($issue) use ($item) {
                return [
                    'File' => $item['file'],
                    'Type' => $issue['type'],
                    'Severity' => str_repeat('⚠', $issue['severity']),
                    'Issue' => $issue['description'],
                    'Solution' => $issue['solution'],
                ];
            });
        })->toArray();

        table($rows);

        return count($rows) > 0 ? 1 : 0;
    }
}
