<?php

use App\Models\Language;
use App\Models\Repo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Spatie\Activitylog\Models\Activity;

it('has a working factory', function () {
    $language = Language::factory()->create();
    expect($language)->toBeInstanceOf(Language::class)
        ->and($language->name)->toBeString();

});

it('must have a unique name', function () {
    // Create a language with a specific name
    $language = Language::factory()->create(['name' => 'Unique Language']);

    // Attempt to create another language with the same name
    $duplicateLanguage = function () {
        Language::factory()->create(['name' => 'Unique Language']);
    };

    // Assert that creating a duplicate throws an exception
    expect($duplicateLanguage)->toThrow(QueryException::class);
});

it('can have many repos', function () {
    $language = Language::factory()->has(Repo::factory(3), 'repos')->create();
    expect($language->repos()->first())->toBeInstanceOf(Repo::class)
        ->and($language->repos())->toBeInstanceOf(HasMany::class)
        ->and($language->repos)->toBeInstanceOf(Collection::class);
});

it('is logged in activity', function () {
    $language = Language::factory()->create();

    $language = $language->fresh(); // Refresh the language to get the latest data

    // Verify that the activity was logged
    $this->assertDatabaseHas('activity_log', [
        'subject_type' => Language::class,
        'subject_id' => $language->id,
        'event' => 'created',
    ]);

    // Verify the activity log data
    $activity = Activity::where([
        'subject_type' => Language::class,
        'subject_id' => $language->id,
        'event' => 'created',
    ])->first();

    expect($activity)->toBeInstanceOf(Activity::class)
        ->and($activity->subject)->toEqual($language)
        ->and($activity->causer)->toBeNull();
});
