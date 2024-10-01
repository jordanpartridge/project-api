<?php

namespace App\Services;

use App\Http\Integrations\Github\Github as GithubIntegration;
use App\Http\Integrations\Github\Requests\GetUserRequest;

class Github
{
    public function __construct(private GithubIntegration $githubIntegration) {}

    public function user()
    {
        return $this->githubIntegration->send(new GetUserRequest);
    }
}
