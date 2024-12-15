<?php

use App\Models\Owner;
use App\Models\Repo;
use Illuminate\Support\Facades\Http;

it('can sync repositories from github', function () {
    // Mock the GitHub API response
    Http::fake([
        'api.github.com/*' => Http::response([
            [
                'id' => '123456',
                'name' => 'test-repo',
                'full_name' => 'test-user/test-repo',
                'owner' => [
                    'id' => '789',
                    'login' => 'test-user',
                    'type' => 'User',
                    'avatar_url' => 'https://example.com/avatar.jpg',
                    'html_url' => 'https://github.com/test-user',
                ],
                'html_url' => 'https://github.com/test-user/test-repo',
                'visibility' => 'public',
                'created_at' => now()->subDays(5)->toISOString(),
                'updated_at' => now()->toISOString(),
            ],
        ]),
    ]);

    // Run the command
    $this->artisan('sync:repos')
        ->assertSuccessful();

    // Assert the data was synced
    expect(Owner::count())->toBe(1)
        ->and(Repo::count())->toBe(1)
        ->and(Owner::first())
        ->login->toBe('test-user')
        ->and(Repo::first())
        ->name->toBe('test-repo');
});
