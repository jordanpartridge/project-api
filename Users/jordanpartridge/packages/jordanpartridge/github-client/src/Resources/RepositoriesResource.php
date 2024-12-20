<?php

namespace JordanPartridge\GithubClient\Resources;

use JordanPartridge\GithubClient\Concerns\HasLanguages;
use JordanPartridge\GithubClient\Contracts\ResourceContract;
use JordanPartridge\GithubClient\Data\Repos\RepoData;
use JordanPartridge\GithubClient\Requests\RepositoryRequest;

class RepositoriesResource implements ResourceContract
{
    use HasLanguages;

    public function __construct(
        private readonly RepositoryRequest $connector,
        private readonly RepoData $repo
    ) {}

    // Other repository-related methods can be added here
}
