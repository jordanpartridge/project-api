<?php

use App\Models\Repo;

it('can be created with factory', function () {
    $repo = Repo::factory()->create();
    expect($repo)->toBeInstanceOf(Repo::class)
        ->and($repo->name)->toBeString()
        ->and($repo->url)->toBeString()
        ->and($repo->project_id)->toBeInstanceOf(\Glhd\Bits\Snowflake::class);
});
