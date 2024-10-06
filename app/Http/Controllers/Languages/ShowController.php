<?php

namespace App\Http\Controllers\Languages;

use App\Http\Resources\LanguageResource;
use App\Models\Language;

class ShowController
{
    public function __invoke(Language $language)
    {
        return new LanguageResource($language);
    }
}
