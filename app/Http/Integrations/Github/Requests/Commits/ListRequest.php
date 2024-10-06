<?php

namespace App\Http\Integrations\Github\Requests\Commits;

use App\Models\Repo;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly Repo $repo,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repos/' . $this->repo->full_name . '/commits';
    }
}
