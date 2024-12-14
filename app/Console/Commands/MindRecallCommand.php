<?php

namespace App\Console\Commands;

use App\Models\Entity;
use Illuminate\Console\Command;

class MindRecallCommand extends Command
{
    protected $signature = 'mind:recall 
        {query : What to recall}
        {--type= : Filter by type}';

    protected $description = 'Recall knowledge from the mind bank';

    public function handle()
    {
        $query = Entity::query()
            ->with('observations')
            ->where('name', 'like', "%{$this->argument('query')}%");

        if ($type = $this->option('type')) {
            $query->where('type', $type);
        }

        $entities = $query->get();

        if ($entities->isEmpty()) {
            $this->info("No knowledge found about '{$this->argument('query')}'");
            return Command::SUCCESS;
        }

        foreach ($entities as $entity) {
            $this->info("\n{$entity->name} ({$entity->type}):");
            foreach ($entity->observations as $observation) {
                $this->line(" - {$observation->content}");
            }
        }

        return Command::SUCCESS;
    }
}