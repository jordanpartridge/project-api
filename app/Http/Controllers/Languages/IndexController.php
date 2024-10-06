<?php

namespace App\Http\Controllers\Languages;

use App\Http\Resources\LanguageResource;
use App\Models\Language;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexController
{
    public function __invoke(): AnonymousResourceCollection
    {
        return LanguageResource::collection(Language::all());
    }
}
