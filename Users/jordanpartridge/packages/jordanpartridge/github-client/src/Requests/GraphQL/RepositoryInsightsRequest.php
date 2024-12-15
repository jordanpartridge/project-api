<?php

namespace JordanPartridge\GithubClient\Requests\GraphQL;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class RepositoryInsightsData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description,
        public string $url,
        public string $createdAt,
        public array $languages,
        public array $contributors,
        public int $starCount,
        public int $forkCount,
        public array $pullRequests
    ) {}
}

class RepositoryInsightsRequest extends AbstractGraphQLRequest
{
    protected string $owner;

    protected string $repo;

    public function __construct(string $owner, string $repo)
    {
        $this->owner = $owner;
        $this->repo = $repo;
    }

    public function definition(): string
    {
        return <<<'GRAPHQL'
        query RepositoryInsights($owner: String!, $repo: String!) {
            repository(owner: $owner, name: $repo) {
                name
                description
                url
                createdAt
                stargazerCount
                forkCount
                
                languages(first: 10, orderBy: {field: SIZE, direction: DESC}) {
                    edges {
                        size
                        node {
                            name
                            color
                        }
                    }
                }
                
                contributors(first: 100, orderBy: {field: CONTRIBUTIONS, direction: DESC}) {
                    totalCount
                    edges {
                        node {
                            name
                            login
                        }
                        contributions {
                            totalCommitContributions
                        }
                    }
                }
                
                pullRequests {
                    totalCount
                    edges {
                        node {
                            state
                            createdAt
                            closedAt
                        }
                    }
                }
            }
        }
        GRAPHQL;
    }

    public function variables(): array
    {
        return [
            'owner' => $this->owner,
            'repo' => $this->repo,
        ];
    }

    protected function parseData(array $data): RepositoryInsightsData
    {
        $repo = $data['repository'];

        return new RepositoryInsightsData(
            name: $repo['name'],
            description: $repo['description'] ?? null,
            url: $repo['url'],
            createdAt: $repo['createdAt'],
            starCount: $repo['stargazerCount'],
            forkCount: $repo['forkCount'],
            languages: array_map(fn ($lang) => [
                'name' => $lang['node']['name'],
                'color' => $lang['node']['color'],
                'size' => $lang['size'],
            ], $repo['languages']['edges']),
            contributors: array_map(fn ($contrib) => [
                'name' => $contrib['node']['name'] ?? $contrib['node']['login'],
                'login' => $contrib['node']['login'],
                'total_commits' => $contrib['contributions']['totalCommitContributions'],
            ], $repo['contributors']['edges']),
            pullRequests: array_map(fn ($pr) => [
                'state' => $pr['node']['state'],
                'created_at' => $pr['node']['createdAt'],
                'closed_at' => $pr['node']['closedAt'],
            ], $repo['pullRequests']['edges'])
        );
    }
}
