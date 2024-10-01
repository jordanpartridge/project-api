<?php

namespace App\Events;

use App\Models\Project;
use App\States\ProjectState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ProjectCreated extends Event
{
    #[StateId(ProjectState::class)]
    public ?int $project_id = null;

    public string $name;

    public ?string $description = null;

    public function applyToProject(ProjectState $project): void
    {
        $project->name = $this->name;
        $project->description = $this->description;
    }

    public function handle()
    {
        return Project::updateOrCreate([
            'name' => $this->name,
        ], [
            'description' => $this->description,
        ]);
    }
}
