<?php

namespace JordanPartridge\GithubClient\DataTransferObjects\Shared;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PaginationDto extends Data
{
    public function __construct(
        #[Validation('required|integer|min:1')]
        public int $page,

        #[Validation('required|integer|min:1|max:100')]
        public int $perPage,

        #[Validation('required|integer|min:0')]
        public int $total,

        #[Validation('required|integer')]
        public int $totalPages
    ) {}

    /**
     * Check if there are more pages
     */
    public function hasMorePages(): bool
    {
        return $this->page < $this->totalPages;
    }

    /**
     * Get the next page number
     */
    public function getNextPage(): ?int
    {
        return $this->hasMorePages() ? $this->page + 1 : null;
    }
}
