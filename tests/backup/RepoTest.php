<?php

use App\Models\Language;
use App\Models\Project;
use App\Models\Repo;

it('can be created with factory', function () {
    $repo = Repo::factory()->create();
    expect($repo)->toBeInstanceOf(Repo::class)
        ->and($repo->name)->toBeString()
        ->and($repo->url)->toBeString();
});

it('belongs to a project', function () {
    $project = Project::factory()->create();
    $repo = Repo::factory()->create();
    $repo->projects()->attach($project->id);
    
    expect($repo->projects)->toHaveCount(1)
        ->and($repo->projects->first())->toBeInstanceOf(Project::class);
});

it('belongs to a language', function () {
    $repo = Repo::factory()->create();
    expect($repo->language)->toBeInstanceOf(Language::class);
});

it('is logged', function () {
    $repo = Repo::factory()->create();
    expect($repo->activities()->count())->toBe(1);
});