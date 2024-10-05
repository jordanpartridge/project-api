<?php

use App\Models\Language;
use App\Models\Project;
use App\Models\Repo;
use Glhd\Bits\Snowflake;

it('can be created with factory', function () {
    $repo = Repo::factory()->create();
    expect($repo)->toBeInstanceOf(Repo::class)
        ->and($repo->name)->toBeString()
        ->and($repo->url)->toBeString()
        ->and($repo->project_id)->toBeInstanceOf(Snowflake::class);
});

it('belongs to a project', function () {
    $repo = Repo::factory()->create();
    expect($repo->project)->toBeInstanceOf(Project::class);
});

it('belongs to a language', function () {
    $repo = Repo::factory()->create();
    expect($repo->language)->toBeInstanceOf(Language::class);
});

it('is logged', function () {
    $repo = Repo::factory()->create();
    expect($repo->activities()->count())->toBe(1);
});
