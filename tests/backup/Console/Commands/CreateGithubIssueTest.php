<?php

use Illuminate\Support\Facades\Http;

it('creates a new GitHub issue with provided parameters', function () {
    Http::fake([
        'api.github.com/repos/test/repo/issues' => Http::response([
            'number' => 1,
            'title' => 'Test Issue',
            'state' => 'open',
            'created_at' => '2024-12-10T00:00:00Z',
            'labels' => [
                ['name' => 'bug'],
            ],
            'assignees' => [
                ['login' => 'octocat'],
            ],
        ], 201),
    ]);

    $this->artisan('github:create-issue', [
        '--repo' => 'test/repo',
        '--title' => 'Test Issue',
        '--body' => 'Test Description',
        '--labels' => ['bug'],
        '--assignees' => ['octocat'],
    ])->assertSuccessful()
        ->expectsOutput('Created issue #1: Test Issue');
});

it('enforces validation of required parameters', function () {
    $this->artisan('github:create-issue', [
        '--title' => 'Test Issue',
    ])->assertFailed()
        ->expectsOutput('Missing required parameter: repo');
});

it('validates input against OpenAPI schema specifications', function () {
    $this->artisan('github:create-issue', [
        '--repo' => 'test/repo',
        '--title' => 'Test Issue',
        '--state' => 'invalid',
    ])->assertFailed()
        ->expectsOutput('Invalid payload: state must be one of: open, closed');
});

it('handles GitHub API rate limiting and errors', function () {
    Http::fake([
        'api.github.com/repos/test/repo/issues' => Http::response([
            'message' => 'API rate limit exceeded',
        ], 403),
    ]);

    $this->artisan('github:create-issue', [
        '--repo' => 'test/repo',
        '--title' => 'Test Issue',
    ])->assertFailed()
        ->expectsOutput('API rate limit exceeded');
});
