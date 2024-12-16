<?php

namespace App\Console\Commands;

use App\Services\CodeAnalysis\PrismAnalyzer;
use Illuminate\Console\Command;

class PrismAnalyzeCommand extends Command
{
    protected $signature = 'prism:analyze {path : Path to file or directory to analyze}';
    protected $description = 'Analyze code using Prism';
    
    public function handle(PrismAnalyzer $analyzer): int
    {
        $path = $this->argument('path');
        
        try {
            $result = $analyzer->analyzeFile($path);
            
            $this->table(
                ['Metric', 'Value'],
                collect($result['metrics'])
                    ->map(fn ($value, $key) => [$key, $value])
                    ->toArray()
            );
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}