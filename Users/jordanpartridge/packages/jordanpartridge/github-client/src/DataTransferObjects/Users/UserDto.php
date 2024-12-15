<?php

namespace JordanPartridge\GithubClient\DataTransferObjects\Users;

use JordanPartridge\GithubClient\DataTransferObjects\Contracts\DataTransformableInterface;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class UserDto extends Data implements DataTransformableInterface
{
    public function __construct(
        #[Validation('required|integer')]
        public int $id,

        #[Validation('required|string')]
        public string $login,

        #[Validation('required|string')]
        public string $nodeId,

        #[Validation('required|url')]
        public string $avatarUrl,

        #[Validation('required|string')]
        public string $type,

        #[Validation('nullable|string')]
        public ?string $name = null,

        #[Validation('nullable|string')]
        public ?string $email = null,

        #[Validation('nullable|string')]
        public ?string $company = null,

        #[Validation('nullable|string')]
        public ?string $blog = null,

        #[Validation('nullable|string')]
        public ?string $location = null,

        #[Validation('nullable|boolean')]
        public ?bool $siteAdmin = null,

        #[Validation('nullable|integer')]
        public ?int $publicRepos = null,

        #[Validation('nullable|integer')]
        public ?int $publicGists = null,

        #[Validation('nullable|integer')]
        public ?int $followers = null,

        #[Validation('nullable|integer')]
        public ?int $following = null,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $createdAt = null
    ) {}

    public static function fromApiResponse(array $data): self
    {
        return new self(
            id: $data['id'],
            login: $data['login'],
            nodeId: $data['node_id'],
            avatarUrl: $data['avatar_url'],
            type: $data['type'],
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            company: $data['company'] ?? null,
            blog: $data['blog'] ?? null,
            location: $data['location'] ?? null,
            siteAdmin: $data['site_admin'] ?? null,
            publicRepos: $data['public_repos'] ?? null,
            publicGists: $data['public_gists'] ?? null,
            followers: $data['followers'] ?? null,
            following: $data['following'] ?? null,
            createdAt: isset($data['created_at'])
                ? new \DateTimeImmutable($data['created_at'])
                : null
        );
    }
}
