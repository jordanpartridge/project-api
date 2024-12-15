<?php

namespace JordanPartridge\GithubClient\DataTransferObjects\Users;

use JordanPartridge\GithubClient\DataTransferObjects\Contracts\DataTransformableInterface;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class GitUserDto extends Data implements DataTransformableInterface
{
    public function __construct(
        #[Validation('required|string')]
        public string $name,

        #[Validation('nullable|string')]
        public ?string $email,

        #[Validation('required|date')]
        public \DateTimeInterface $date
    ) {}

    public static function fromApiResponse(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            email: $data['email'] ?? null,
            date: new \DateTimeImmutable($data['date'])
        );
    }
}
