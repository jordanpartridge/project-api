<?php

use App\Models\Commit;

it('can be created with factory', function () {
    $commit = Commit::factory()->create();
    expect($commit)->toBeInstanceOf(Commit::class)
        ->and($commit->sha)->toBeString()
        ->and($commit->message)->toBeString()
        ->and($commit->author)->toBeString()
        ->and($commit->repo_id)->toBeInt();
});
