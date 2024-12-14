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

        info('🔍 Code Analysis');

        $criticalIssues = [];
        $opportunities = [];

        foreach (File::files($path) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $filename = basename($file);
            $results = $analyzer->analyze(File::get($file), $filename);

            if (! empty($results['critical'])) {
                $criticalIssues[] = ['file' => $filename, 'issues' => $results['critical']];
            }

            if (! empty($results['opportunities'])) {
                $opportunities[] = ['file' => $filename, 'items' => $results['opportunities']];
            }
        }

        if (! empty($criticalIssues)) {
            info('⚠️  Critical Fixes Needed');
            table(collect($criticalIssues)->flatMap(function ($item) {
                return collect($item['issues'])->map(function ($issue) use ($item) {
                    return [
                        'File' => $item['file'],
                        'Issue' => $issue['description'],
                        'Fix' => $issue['solution'],
                    ];
                });
            })->toArray());
        }

        if (! empty($opportunities)) {
            info('💡 Top Opportunities');
            table(collect($opportunities)->flatMap(function ($item) {
                return collect($item['items'])->map(function ($opp) use ($item) {
                    return [
                        'File' => $item['file'],
                        'Type' => $opp['type'],
                        'Improvement' => $opp['description'],
                    ];
                });
            })->toArray());
        }

        return ! empty($criticalIssues);
    }
}
