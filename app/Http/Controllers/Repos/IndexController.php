<?php

namespace App\Http\Controllers\Repos;

use App\Http\Resources\RepoResource;
use App\Models\Language;
use App\Models\Repo;

class IndexController
{
    public function __invoke(?Language $language = null)
    {
        return RepoResource::collection($language ? $language->repos : Repo::all());
    }
}
