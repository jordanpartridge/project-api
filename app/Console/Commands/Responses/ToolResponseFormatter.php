<?php

namespace App\Console\Commands\Responses;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ToolResponseFormatter
{
    protected Collection $sections;

    protected array $metrics;

    protected array $recommendations;

    public function __construct()
    {
        $this->sections = collect();
        $this->metrics = [];
        $this->recommendations = [];
    }

    public function addSection(string $title, string $content): self
    {
        $this->sections->push([
            'title' => $title,
            'content' => $content,
            'type' => 'text',
        ]);

        return $this;
    }

    public function addMetric(string $label, mixed $value, ?string $trend = null): self
    {
        $this->metrics[] = [
            'label' => $label,
            'value' => $value,
            'trend' => $trend,
        ];

        return $this;
    }

    public function addRecommendation(string $title, string $description, string $priority = 'medium'): self
    {
        $this->recommendations[] = [
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
        ];

        return $this;
    }

    public function displayInCommand(Command $command): void
    {
        // Display sections
        foreach ($this->sections as $section) {
            $command->newLine();
            $command->info(str_repeat('=', strlen($section['title'])));
            $command->info($section['title']);
            $command->info(str_repeat('=', strlen($section['title'])));
            $command->line($section['content']);
        }

        // Display metrics if any
        if (! empty($this->metrics)) {
            $command->newLine();
            $command->info('Metrics');
            $command->info('-------');
            foreach ($this->metrics as $metric) {
                $trend = match ($metric['trend']) {
                    'up' => '↑',
                    'down' => '↓',
                    default => '-'
                };
                $command->line(sprintf(
                    '%s: %s %s',
                    $metric['label'],
                    $metric['value'],
                    $trend
                ));
            }
        }

        // Display recommendations if any
        if (! empty($this->recommendations)) {
            $command->newLine();
            $command->info('Recommendations');
            $command->info('---------------');
            foreach ($this->recommendations as $rec) {
                $command->line(sprintf(
                    '● %s (%s priority)',
                    $rec['title'],
                    $rec['priority']
                ));
                $command->line('  '.$rec['description']);
                $command->newLine();
            }
        }
    }

    public function toArray(): array
    {
        return [
            'sections' => $this->sections->toArray(),
            'metrics' => $this->metrics,
            'recommendations' => $this->recommendations,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
