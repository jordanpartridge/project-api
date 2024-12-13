<?php

use Illuminate\Support\Facades\Http;

it('handles GitHub API rate limiting and errors', function () {
    Http::fake([
        'api.github.com/repos/test/repo/issues' => Http::response([
            'message' => 'API rate limit exceeded'
        ], 403)
    ]);

    $this->artisan('github:issues', [
        'action' => 'create',
        '--repo' => 'test/repo',
        '--title' => 'Test Issue'
    ])->assertFailed()
      ->expectsOutput('API rate limit exceeded');
});
