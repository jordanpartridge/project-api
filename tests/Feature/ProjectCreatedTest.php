<?php

use App\Events\ProjectCreated;
use App\Models\Project;
use App\States\ProjectState;

it('creates a new project', function () {
    $event = new ProjectCreated;
    $event->name = 'Test Project';
    $event->description = 'This is a test project';

    $project = $event->handle();

    expect($project)->toBeInstanceOf(Project::class)
        ->and($project->name)->toBe('Test Project')
        ->and($project->description)->toBe('This is a test project');
});

it('applies changes to project state', function () {
    $event = new ProjectCreated;
    $event->name = 'Test Project';
    $event->description = 'This is a test project';

    $projectState = new ProjectState;
    $event->applyToProject($projectState);

    expect($projectState->name)->toBe('Test Project')
        ->and($projectState->description)->toBe('This is a test project');
});

it('handles null description', function () {
    $event = new ProjectCreated;
    $event->name = 'Test Project';
    $event->description = null;

    $project = $event->handle();

    expect($project)->toBeInstanceOf(Project::class)
        ->and($project->name)->toBe('Test Project')
        ->and($project->description)->toBeNull();
});
