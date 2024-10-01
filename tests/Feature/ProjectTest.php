<?php

use App\Events\ProjectCreated;
use App\Models\Project;

it('has valid factory', function () {
    $project = Project::factory()->create();
    expect($project)->toBeInstanceOf(Project::class);
});

it('can be created through the event', function () {
    $event = ProjectCreated::commit(
        name: 'My Project',
        description: 'My Project Description',
    );

    $project = Project::where('name', 'My Project')->first();
    expect($project)->toBeInstanceOf(Project::class);
});
