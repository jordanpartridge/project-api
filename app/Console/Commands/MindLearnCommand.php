<?php

namespace App\Console\Commands;

use App\Models\Entity;
use Illuminate\Console\Command;

class MindLearnCommand extends Command
{
    protected $signature = 'mind:learn 
        {entity : Name of the entity}
        {observation : What to remember about it}
        {--type=concept : Type of entity}
        {--confidence=high : Confidence level}';

    protected $description = 'Store new knowledge in the mind bank';

    public function handle()
    {
        $entity = Entity::firstOrCreate(
            ['name' => $this->argument('entity')],
            ['type' => $this->option('type')]
        );

        $observation = $entity->observations()->create([
            'content' => $this->argument('observation'),
            'confidence' => $this->option('confidence')
        ]);

        $this->info("Learned about '{$entity->name}': {$observation->content}");

        return Command::SUCCESS;
    }
}