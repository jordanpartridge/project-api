<?php

namespace App\Console\Commands;

use App\Console\Commands\Responses\ToolResponseFormatter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ProjectStructureCommand extends Command
{
    protected $signature = 'projects:structure {base_path?}';
    protected $description = 'Display the structure and status of projects in the Sites directory';

    protected $basePath;
    protected $response;

    public function handle()
    {
        $this->basePath = $this->argument('base_path') ?? '/Users/jordanpartridge/Sites';
        $this->response = new ToolResponseFormatter;

        // Add overview section
        $this->response->addSection(
            'Projects Overview',
            "Base Path: {$this->basePath}\n" .
            'Total Projects: ' . $this->countProjects() . "\n" .
            'Last Scan: ' . now()->format('Y-m-d H:i:s')
        );

        // Add production projects
        $this->addProductionProjects();

        // Add development tools
        $this->addDevelopmentTools();

        // Add metrics
        $this->addProjectMetrics();

        // Display everything
        $this->response->displayInCommand($this);

        return Command::SUCCESS;
    }

    protected function countProjects(): int
    {
        return collect(File::directories($this->basePath))
            ->filter(fn ($dir) => File::exists($dir . '/.git'))
            ->count();
    }

    protected function addProductionProjects(): void
    {
        $productionPath = $this->basePath . '/production/apis';
        if (! File::exists($productionPath)) {
            return;
        }

        $content = collect(File::directories($productionPath))
            ->map(function ($dir) {
                $name = basename($dir);
                $gitPath = $dir . '/.git';
                $composerPath = $dir . '/composer.json';
                $status = File::exists($gitPath) ? '🟢' : '⚪️';

                $stack = 'Laravel';
                if (File::exists($composerPath)) {
                    $composer = json_decode(File::get($composerPath), true);
                    if (isset($composer['require'])) {
                        $stack .= $this->getStackFromComposer($composer['require']);
                    }
                }

                return sprintf(
                    "%s %s\n   Path: %s\n   Stack: %s\n",
                    $status,
                    $name,
                    str_replace($this->basePath, '', $dir),
                    $stack
                );
            })
            ->implode("\n");

        $this->response->addSection('Production APIs', $content);
    }

    protected function addDevelopmentTools(): void
    {
        $toolsContent = collect(['claude-tools', 'claude-context'])
            ->map(function ($name) {
                $dir = $this->basePath . '/' . $name;
                if (! File::exists($dir)) {
                    return null;
                }

                $status = File::exists($dir . '/.git') ? '🟢' : '⚪️';

                return sprintf(
                    "%s %s\n   Path: /%s\n   Purpose: %s\n",
                    $status,
                    $name,
                    $name,
                    $this->getProjectPurpose($name)
                );
            })
            ->filter()
            ->implode("\n");

        $this->response->addSection('Development Tools', $toolsContent);
    }

    protected function addProjectMetrics(): void
    {
        $activeCount = collect(File::directories($this->basePath))
            ->filter(fn ($dir) => File::exists($dir . '/.git'))
            ->count();

        $this->response->addMetric('Active Projects', $activeCount)
            ->addMetric('Production APIs', collect(File::directories($this->basePath . '/production/apis'))->count())
            ->addMetric('Development Tools', 2);
    }

    protected function getStackFromComposer(array $require): string
    {
        $stack = [];
        if (isset($require['filament/filament'])) {
            $stack[] = 'Filament';
        }
        if (isset($require['livewire/livewire'])) {
            $stack[] = 'Livewire';
        }

        return empty($stack) ? '' : ', ' . implode(', ', $stack);
    }

    protected function getProjectPurpose(string $name): string
    {
        return match ($name) {
            'claude-tools' => 'Tools and utilities for Claude AI integration',
            'claude-context' => 'Context management system for Claude AI',
            default => 'Development tool'
        };
    }
}
