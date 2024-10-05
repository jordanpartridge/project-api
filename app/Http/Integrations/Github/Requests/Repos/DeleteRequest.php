<?php

namespace App\Http\Integrations\Github\Requests\Repos;

use App\Models\Repo;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteRequest extends Request
{
    public Method $method = Method::DELETE;

    public function __construct(
        protected Repo $repo,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repos/' . $this->repo->full_name;
    }
}
