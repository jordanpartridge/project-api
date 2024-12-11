<?php

namespace App\Services\GitHub;

use Illuminate\Support\Facades\Http;
use App\Exceptions\GitHub\GitHubProjectException;

class GitHubProjectService extends GitHubService
{
    protected function executeQuery($query)
    {
        $response = Http::withToken(config())
            ->post($this->baseUrl, [
                'query' => $query,
            ]);

        if ($response->failed()) {
            throw new GitHubProjectException('GitHub Projects API request failed: ' . $response->status());
        }

        return $response->json();
    }
}