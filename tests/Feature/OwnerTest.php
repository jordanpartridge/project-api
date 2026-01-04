<?php

use App\Models\Owner;
use App\Models\Repo;

it('has basic owner attributes', function () {
    $owner = Owner::factory()->create();

    expect($owner)
        ->toBeInstanceOf(Owner::class)
        ->github_id->not->toBeEmpty()
        ->login->not->toBeEmpty()
        ->type->toBe('user');
});

it('can be created as a user', function () {
    $owner = Owner::factory()->user()->create();
    expect($owner->type)->toBe('user');
});

it('can be created as an organization', function () {
    $owner = Owner::factory()->organization()->create();
    expect($owner->type)->toBe('organization');
});

it('can have many repositories', function () {
    $owner = Owner::factory()->create();
    $repos = Repo::factory(3)->create(['owner_id' => $owner->id]);

    expect($owner->repos)->toHaveCount(3);
    expect($owner->repos->first())->toBeInstanceOf(Repo::class);
});

it('can be soft deleted', function () {
    $owner = Owner::factory()->create();
    $owner->delete();

    expect(Owner::withTrashed()->find($owner->id))->not->toBeNull()
        ->and(Owner::find($owner->id))->toBeNull();
});

it('cascades deletes to repositories', function () {
    $owner = Owner::factory()->create();
    $repos = Repo::factory(3)->create(['owner_id' => $owner->id]);

    $owner->delete();

    expect(Repo::count())->toBe(0)
        ->and(Repo::withTrashed()->count())->toBe(3);
});