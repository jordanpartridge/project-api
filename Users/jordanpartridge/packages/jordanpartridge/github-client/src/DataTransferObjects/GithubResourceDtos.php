<?php

namespace JordanPartridge\GithubClient\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class UserDto extends Data
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
}

#[MapName(SnakeCaseMapper::class)]
class RepositoryDto extends Data
{
    public function __construct(
        #[Validation('required|integer')]
        public int $id,

        #[Validation('required|string')]
        public string $nodeId,

        #[Validation('required|string')]
        public string $name,

        #[Validation('required|string')]
        public string $fullName,

        #[Validation('nullable|string')]
        public ?string $description,

        #[Validation('required|boolean')]
        public bool $private,

        #[Validation('required|url')]
        public string $htmlUrl,

        #[Validation('nullable|url')]
        public ?string $homepage,

        public UserDto $owner,

        public RepositoryStatsDto $stats,

        public RepositoryPermissionsDto $permissions,

        #[Validation('required|string')]
        public string $defaultBranch,

        #[Validation('nullable|string')]
        public ?string $language,

        #[Validation('required|date')]
        public \DateTimeInterface $createdAt,

        #[Validation('required|date')]
        public \DateTimeInterface $updatedAt,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $pushedAt = null,

        #[Validation('nullable|boolean')]
        public ?bool $archived = null,

        #[Validation('nullable|boolean')]
        public ?bool $disabled = null,

        #[Validation('nullable|string')]
        public ?string $visibility = null,

        public ?RepositoryLicenseDto $license = null,

        #[Validation('nullable|array')]
        public ?array $topics = null
    ) {}
}

#[MapName(SnakeCaseMapper::class)]
class RepositoryStatsDto extends Data
{
    public function __construct(
        #[Validation('required|integer')]
        public int $stargazersCount,

        #[Validation('required|integer')]
        public int $watchersCount,

        #[Validation('required|integer')]
        public int $forksCount,

        #[Validation('nullable|integer')]
        public ?int $openIssuesCount = null
    ) {}
}

#[MapName(SnakeCaseMapper::class)]
class RepositoryPermissionsDto extends Data
{
    public function __construct(
        #[Validation('required|boolean')]
        public bool $admin,

        #[Validation('required|boolean')]
        public bool $push,

        #[Validation('required|boolean')]
        public bool $pull
    ) {}
}

#[MapName(SnakeCaseMapper::class)]
class RepositoryLicenseDto extends Data
{
    public function __construct(
        #[Validation('required|string')]
        public string $key,

        #[Validation('required|string')]
        public string $name,

        #[Validation('required|string')]
        public string $spdxId,

        #[Validation('nullable|url')]
        public ?string $url,

        #[Validation('nullable|string')]
        public ?string $nodeId = null
    ) {}
}

#[MapName(SnakeCaseMapper::class)]
class IssueDto extends Data
{
    public function __construct(
        #[Validation('required|integer')]
        public int $id,

        #[Validation('required|string')]
        public string $nodeId,

        #[Validation('required|integer')]
        public int $number,

        #[Validation('required|string')]
        public string $title,

        #[Validation('required|string')]
        public string $state,

        #[Validation('nullable|string')]
        public ?string $body,

        public UserDto $user,

        public ?UserDto $assignee,

        #[Validation('nullable|array')]
        public ?array $labels,

        public ?MilestoneDto $milestone,

        #[Validation('required|date')]
        public \DateTimeInterface $createdAt,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $closedAt = null,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $updatedAt = null
    ) {}
}

#[MapName(SnakeCaseMapper::class)]
class MilestoneDto extends Data
{
    public function __construct(
        #[Validation('required|integer')]
        public int $id,

        #[Validation('required|integer')]
        public int $number,

        #[Validation('required|string')]
        public string $title,

        #[Validation('nullable|string')]
        public ?string $description,

        #[Validation('required|string')]
        public string $state,

        public UserDto $creator,

        #[Validation('nullable|integer')]
        public ?int $openIssues = null,

        #[Validation('nullable|integer')]
        public ?int $closedIssues = null,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $createdAt = null,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $updatedAt = null,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $dueOn = null,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $closedAt = null
    ) {}
}

#[MapName(SnakeCaseMapper::class)]
class PullRequestDto extends Data
{
    public function __construct(
        #[Validation('required|integer')]
        public int $id,

        #[Validation('required|string')]
        public string $nodeId,

        #[Validation('required|integer')]
        public int $number,

        #[Validation('required|string')]
        public string $state,

        #[Validation('required|string')]
        public string $title,

        #[Validation('nullable|string')]
        public ?string $body,

        public UserDto $user,

        public UserDto $author,

        public ?UserDto $assignee,

        #[Validation('nullable|array')]
        public ?array $labels,

        #[Validation('required|date')]
        public \DateTimeInterface $createdAt,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $updatedAt = null,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $closedAt = null,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $mergedAt = null,

        public ?UserDto $mergedBy = null,

        #[Validation('nullable|string')]
        public ?string $mergeCommitSha = null,

        #[Validation('nullable|string')]
        public ?string $base = null,

        #[Validation('nullable|string')]
        public ?string $head = null,

        #[Validation('nullable|string')]
        public ?string $htmlUrl = null,

        #[Validation('nullable|string')]
        public ?string $diffUrl = null,

        #[Validation('nullable|string')]
        public ?string $patchUrl = null,

        #[Validation('nullable|boolean')]
        public ?bool $draft = null,

        #[Validation('nullable|boolean')]
        public ?bool $mergeable = null,

        #[Validation('nullable|string')]
        public ?string $mergeableState = null
    ) {}
}

#[MapName(SnakeCaseMapper::class)]
class CommitDto extends Data
{
    public function __construct(
        #[Validation('required|string')]
        public string $sha,

        #[Validation('required|string')]
        public string $nodeId,

        public CommitDetailsDto $commit,

        public UserDto $author,

        public UserDto $committer,

        #[Validation('nullable|array')]
        public ?array $parents = null,

        #[Validation('nullable|string')]
        public ?string $url = null,

        #[Validation('nullable|string')]
        public ?string $htmlUrl = null,

        #[Validation('nullable|string')]
        public ?string $commentCount = null
    ) {}
}

#[MapName(SnakeCaseMapper::class)]
class CommitDetailsDto extends Data
{
    public function __construct(
        #[Validation('required|string')]
        public string $message,

        public CommitUserDto $author,

        public CommitUserDto $committer,

        #[Validation('nullable|string')]
        public ?string $url = null,

        #[Validation('nullable|integer')]
        public ?int $commentCount = null,

        #[Validation('nullable|array')]
        public ?array $verification = null
    ) {}
}

#[MapName(SnakeCaseMapper::class)]
class CommitUserDto extends Data
{
    public function __construct(
        #[Validation('required|string')]
        public string $name,

        #[Validation('nullable|string')]
        public ?string $email,

        #[Validation('required|date')]
        public \DateTimeInterface $date
    ) {}
}

#[MapName(SnakeCaseMapper::class)]
class ReleaseDto extends Data
{
    public function __construct(
        #[Validation('required|integer')]
        public int $id,

        #[Validation('required|string')]
        public string $nodeId,

        #[Validation('required|string')]
        public string $tagName,

        #[Validation('required|string')]
        public string $name,

        #[Validation('nullable|string')]
        public ?string $body,

        #[Validation('required|boolean')]
        public bool $draft,

        #[Validation('required|boolean')]
        public bool $prerelease,

        public UserDto $author,

        #[Validation('required|date')]
        public \DateTimeInterface $createdAt,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $publishedAt = null,

        #[Validation('nullable|array')]
        public ?array $assets = null
    ) {}
}

#[MapName(SnakeCaseMapper::class)]
class WorkflowDto extends Data
{
    public function __construct(
        #[Validation('required|integer')]
        public int $id,

        #[Validation('required|string')]
        public string $nodeId,

        #[Validation('required|string')]
        public string $name,

        #[Validation('required|string')]
        public string $state,

        #[Validation('nullable|string')]
        public ?string $path = null,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $createdAt = null,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $updatedAt = null
    ) {}
}

#[MapName(SnakeCaseMapper::class)]
class WorkflowRunDto extends Data
{
    public function __construct(
        #[Validation('required|integer')]
        public int $id,

        #[Validation('required|string')]
        public string $nodeId,

        #[Validation('required|string')]
        public string $name,

        #[Validation('required|string')]
        public string $status,

        #[Validation('required|string')]
        public string $conclusion,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $createdAt = null,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $updatedAt = null,

        #[Validation('nullable|date')]
        public ?\DateTimeInterface $runStartedAt = null,

        #[Validation('nullable|string')]
        public ?string $headBranch = null,

        #[Validation('nullable|string')]
        public ?string $headSha = null,

        public ?UserDto $triggeredBy = null
    ) {}
}
