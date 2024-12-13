<?php

use App\Models\Language;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('has a index route that works empty', function () {
    Sanctum::actingAs(User::factory()->create());
    $response = $this->getJson(route('v1:languages:index'));
    expect($response->json())->toBeArray()->toHaveKey('data');
    $response->assertStatus(200);
});

it('has a index route that works with data', function () {
    Sanctum::actingAs(User::factory()->create());
    $languages = Language::factory(15)->create();
    $response = $this->getJson(route('v1:languages:index'));
    $response->assertJsonCount(15, 'data');
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'name',
            ],
        ],
    ]);
    $response->assertStatus(200);
    $response->assertJson([
        'data' => $languages->select('name')->toArray(),
    ]);
});

it('has a show route that works', function () {
    Sanctum::actingAs(User::factory()->create());
    $language = Language::factory()->create();
    $response = $this->getJson(route('v1:languages:show', $language));
    $response->assertJson([
        'data' => [
            'name' => $language->name,
        ],
    ]);
    $response->assertStatus(200);
});
