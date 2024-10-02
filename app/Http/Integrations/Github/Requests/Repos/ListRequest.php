<?php

namespace App\Http\Integrations\Github\Requests\Repos;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/user/repos';
    }
}
