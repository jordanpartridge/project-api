<?php

namespace JordanPartridge\GithubClient\DataTransferObjects\Shared;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation;

#[MapName(SnakeCaseMapper::class)]
class DateRangeDto extends Data
{
    public function __construct(
        #[Validation('required|date')]
        public \DateTimeInterface $created,

        #[Validation('required|date')]
        public \DateTimeInterface $updated,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $closed = null
    ) {}

    /**
     * Calculate duration between creation and update/closure
     * 
     * @return \DateInterval|null
     */
    public function getDuration(): ?\DateInterval
    {
        $endDate = $this->closed ?? new \DateTimeImmutable();
        return $this->created->diff($endDate);
    }

    /**
     * Check if the item is still active
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->closed === null;
    }
}