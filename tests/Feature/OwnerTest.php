<?php

use App\Models\Issue;
use App\Models\Owner;
use App\Models\PullRequest;
use App\Models\Repo;

beforeEach(function () {
    $this->owner = Owner::factory()->create();
});

it('has basic owner attributes', function () {
    expect($this->owner)
        ->toBeInstanceOf(Owner::class)
        ->github_id->toBeString()
        ->login->toBeString()
        ->type->toBeIn(['User', 'Organization'])
        ->avatar_url->toBeString()
        ->html_url->toBeString();
});

it('can be created as a user', function () {
    $user = Owner::factory()->user()->create();
    expect($user->type)->toBe('User');
});

it('can be created as an organization', function () {
    $org = Owner::factory()->organization()->create();
    expect($org->type)->toBe('Organization');
});

it('can have many repositories', function () {
    $repos = Repo::factory()->count(3)->create([
        'owner_id' => $this->owner->id,
    ]);

    expect($this->owner->repos)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Repo::class);
});

it('can have many authored issues', function () {
    $repo = Repo::factory()->create(['owner_id' => $this->owner->id]);

    $issues = Issue::factory()->count(2)->create([
        'repo_id' => $repo->id,
        'author_id' => $this->owner->id,
    ]);

    expect($this->owner->issues)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Issue::class);
});

it('can have many authored pull requests', function () {
    $repo = Repo::factory()->create(['owner_id' => $this->owner->id]);

    $prs = PullRequest::factory()->count(2)->create([
        'repo_id' => $repo->id,
        'author_id' => $this->owner->id,
    ]);

    expect($this->owner->pullRequests)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(PullRequest::class);
});

it('can be soft deleted', function () {
    $this->owner->delete();

    expect(Owner::count())->toBe(0)
        ->and(Owner::withTrashed()->count())->toBe(1)
        ->and(Owner::withTrashed()->first())->toBeInstanceOf(Owner::class);
});

it('cascades deletes to repositories', function () {
    $repos = Repo::factory()->count(2)->create([
        'owner_id' => $this->owner->id,
    ]);

    $this->owner->delete();

    expect(Repo::count())->toBe(0)
        ->and(Repo::withTrashed()->count())->toBe(2);
});
