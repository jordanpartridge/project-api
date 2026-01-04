<?php

use App\Models\Owner;
use App\Models\Repo;
use App\Models\Language;

beforeEach(function () {
    $this->owner = Owner::factory()->create();
    $this->language = Language::factory()->create(['name' => 'PHP']);
});

it('can be created with factory', function () {
    $repo = Repo::factory()
        ->for($this->owner)
        ->for($this->language)
        ->create();

    expect($repo)
        ->name->not->toBeEmpty()
        ->full_name->not->toBeEmpty()
        ->owner_id->toBe($this->owner->id)
        ->language_id->toBe($this->language->id);
});

it('belongs to a project', function () {
    $repo = Repo::factory()
        ->for($this->owner)
        ->for($this->language)
        ->hasProject()
        ->create();

    expect($repo->project)
        ->not->toBeNull()
        ->name->not->toBeEmpty();
});

it('belongs to a language', function () {
    $repo = Repo::factory()
        ->for($this->owner)
        ->for($this->language)
        ->create();

    expect($repo->language)
        ->not->toBeNull()
        ->name->toBe('PHP');
});

it('is logged in activity log', function () {
    $repo = Repo::factory()
        ->for($this->owner)
        ->for($this->language)
        ->create();

    expect(activity()->where('subject_type', Repo::class)->exists())
        ->toBeTrue();
});