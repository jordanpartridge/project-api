<?php

namespace App\Console\Commands;

use App\Services\CodeAnalysis\StreamingAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

class PrismAnalyzeCommand extends Command
{
    protected $signature = 'prism:analyze {path?}';
    protected $description = 'AI-powered code analysis using Prism';

    public function handle(StreamingAnalyzer $analyzer)
    {
        $path = $this->argument('path') ?? app_path();
        $files = collect(File::files($path))->filter(fn ($f) => pathinfo($f, PATHINFO_EXTENSION) === 'php');
        $results = [];

        info('🔍 Code Analysis');

        foreach ($files as $file) {
            $filename = basename($file);
            info("\n📄 {$filename}");

            $code = File::get($file);
            foreach ($analyzer->analyzeStream($code, $filename) as $analysis) {
                $issues = $analysis['result']['issues'] ?? [];
                $critical = collect($issues)->where('severity', '>=', 4);

                if ($critical->isNotEmpty()) {
                    table([
                        'Type' => $analysis['area'],
                        'Issue' => $critical->first()['description'],
                        'Fix' => $critical->first()['solution'],
                    ]);
                }
            }
        }
    }
}
