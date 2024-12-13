<?php

use App\Models\Owner;

it('has a working factory', function () {
    $owner = Owner::factory()->create();
    expect($owner)->toBeInstanceOf(Owner::class);
});
