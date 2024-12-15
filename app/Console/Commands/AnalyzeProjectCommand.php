<?php

namespace App\Console\Commands;

use App\Console\Commands\Responses\ToolResponseFormatter;
use Illuminate\Console\Command;

class AnalyzeProjectCommand extends Command
{
    protected $signature = 'project:analyze {path}';

    protected $description = 'Analyzes a project directory and provides insights';

    public function handle()
    {
        $path = $this->argument('path');

        // Simulate tool analysis results
        $results = $this->analyzeProject($path);

        $response = new ToolResponseFormatter;

        // Add project overview section
        $response->addSection(
            'Project Overview',
            "Analyzed project at: {$path}\n".
            "Total files: {$results['fileCount']}\n".
            "Last modified: {$results['lastModified']}"
        );

        // Add key metrics
        $response->addMetric('Code Coverage', '87%', 'up')
            ->addMetric('Technical Debt', '12 hours', 'down')
            ->addMetric('Test Count', '156', 'up');

        // Add recommendations based on analysis
        foreach ($results['recommendations'] as $rec) {
            $response->addRecommendation(
                $rec['title'],
                $rec['description'],
                $rec['priority']
            );
        }

        // Display formatted output
        $response->displayInCommand($this);

        // You can also get the data as array or JSON for storage/further processing
        $dataForStorage = $response->toArray();
    }

    private function analyzeProject(string $path): array
    {
        // Simulate project analysis
        return [
            'fileCount' => 234,
            'lastModified' => '2024-12-08',
            'recommendations' => [
                [
                    'title' => 'Update Dependencies',
                    'description' => 'Several packages are outdated by more than 2 major versions',
                    'priority' => 'high',
                ],
                [
                    'title' => 'Increase Test Coverage',
                    'description' => 'Models/UserPreference.php lacks sufficient test coverage',
                    'priority' => 'medium',
                ],
            ],
        ];
    }
}
