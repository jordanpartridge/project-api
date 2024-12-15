<?php

namespace JordanPartridge\GithubClient\Concerns;

use JordanPartridge\GithubClient\Data\Repos\LanguagesData;

trait HasLanguages
{
    /**
     * Fetch repository languages
     */
    public function languages(): LanguagesData
    {
        // Assuming $this->connector is the HTTP client
        $response = $this->connector
            ->withUrlSegment($this->repo->full_name.'/languages')
            ->send('GET');

        return LanguagesData::from(
            languages: $response->json(),
            primaryLanguage: $this->repo->language
        );
    }
}
