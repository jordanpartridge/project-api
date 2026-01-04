<?php

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class
)->in('Feature', 'Unit');

// Global test expectations
expect()->extend('toBeValidResponse', function () {
    return $this->value
        ->status()->toBe(200)
        ->json()->toHaveKeys(['status', 'data']);
});