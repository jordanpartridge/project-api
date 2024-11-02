<?php

namespace App\Services;

use App\Http\Integrations\Github\Requests\Commits\ListRequest;
use App\Models\Repo;
use JordanPartridge\GithubClient\Contracts\GithubConnectorInterface;
use JordanPartridge\GithubClient\Requests\Repos\Repos;
use JordanPartridge\GithubClient\Requests\Repos\Repos\Delete;
use JordanPartridge\GithubClient\Requests\User;
use Saloon\Http\Response;

final readonly class Github
{
    public function __construct(private GithubConnectorInterface $githubIntegration) {}

    public function user(): Response
    {
        return $this->githubIntegration->send(new User);
    }

    public function repos(): Response
    {
        return $this->githubIntegration->send(new Repos);
    }

    public function commits(Repo $repo): Response
    {
        return $this->githubIntegration->send(new ListRequest($repo));
    }

    public function deleteRepo(Repo $repo): Response
    {
        return $this->githubIntegration->send(new Delete($repo->full_name));
    }
}
