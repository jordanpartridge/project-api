<?php

namespace App\Services;

use App\Http\Integrations\Github\Github as GithubIntegration;
use App\Http\Integrations\Github\Requests\Repos\ListRequest;
use App\Http\Integrations\Github\Requests\User\GetUserRequest;
use Saloon\Http\Response;

class Github
{
    public function __construct(private GithubIntegration $githubIntegration) {}

    public function user(): Response
    {
        return $this->githubIntegration->send(new GetUserRequest);
    }

    public function repos(): Response
    {
        return $this->githubIntegration->send(new ListRequest);
    }
}
