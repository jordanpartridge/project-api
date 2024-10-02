<?php

use App\Models\Language;

it('has a working factory', function () {
    $language = Language::factory()->create();
    expect($language)->toBeInstanceOf(Language::class)
        ->and($language->name)->toBeString();

});
