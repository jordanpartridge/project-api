<?php

namespace JordanPartridge\GithubClient\Data\Repos;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class LanguagesData extends Data
{
    /**
     * Construct a languages data object
     * 
     * @param array<string, int> $languages Associative array of language names to byte counts
     */
    public function __construct(
        public array $languages,
        public ?string $primaryLanguage = null,
        public ?float $primaryLanguagePercentage = null,
    ) {
        // Automatically determine primary language if not provided
        if ($this->primaryLanguage === null && !empty($languages)) {
            $this->primaryLanguage = array_key_first($languages);
        }

        // Calculate primary language percentage
        if ($this->primaryLanguagePercentage === null) {
            $totalBytes = array_sum($languages);
            $this->primaryLanguagePercentage = $totalBytes > 0 
                ? ($languages[$this->primaryLanguage] / $totalBytes) * 100 
                : 0.0;
        }
    }

    /**
     * Get languages sorted by byte count (descending)
     * 
     * @return array<string, int>
     */
    public function getSortedLanguages(): array
    {
        arsort($this->languages);
        return $this->languages;
    }

    /**
     * Get language percentages
     * 
     * @return array<string, float>
     */
    public function getLanguagePercentages(): array
    {
        $totalBytes = array_sum($this->languages);
        return array_map(fn($bytes) => ($bytes / $totalBytes) * 100, $this->languages);
    }
}
