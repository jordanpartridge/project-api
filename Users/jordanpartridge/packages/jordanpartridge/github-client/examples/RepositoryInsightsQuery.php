<?php

use JordanPartridge\GithubClient\Facades\GitHub;

class RepositoryInsightsDemo 
{
    public function getComprehensiveRepositoryInsights(string $owner, string $repo)
    {
        // Comprehensive repository analysis query
        $insights = GitHub::graphql()->query(
            query: <<<'GRAPHQL'
            query RepositoryInsights($owner: String!, $repo: String!) {
                repository(owner: $owner, name: $repo) {
                    # Basic Repository Information
                    name
                    description
                    url
                    createdAt
                    
                    # Quantitative Metrics
                    stargazerCount
                    forkCount
                    watchers {
                        totalCount
                    }
                    
                    # Language Composition
                    languages(first: 10, orderBy: {field: SIZE, direction: DESC}) {
                        edges {
                            size
                            node {
                                name
                                color
                            }
                        }
                        totalCount
                    }
                    
                    # Contributor Insights
                    contributors(first: 100, orderBy: {field: CONTRIBUTIONS, direction: DESC}) {
                        totalCount
                        edges {
                            contributions {
                                totalCommitContributions
                            }
                            node {
                                name
                                email
                                login
                            }
                        }
                    }
                    
                    # Recent Activity
                    defaultBranchRef {
                        target {
                            ... on Commit {
                                history(first: 50) {
                                    totalCount
                                    edges {
                                        node {
                                            committedDate
                                            message
                                            author {
                                                name
                                                email
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    # Pull Request Health
                    pullRequests {
                        totalCount
                        edges {
                            node {
                                state
                                createdAt
                                closedAt
                                mergeable
                            }
                        }
                    }
                }
            }
            GRAPHQL,
            variables: [
                'owner' => $owner,
                'repo' => $repo
            ]
        );

        return $this->transformInsights($insights);
    }

    protected function transformInsights(array $rawInsights): array
    {
        // Transform raw GraphQL response into a structured, easy-to-use format
        $repo = $rawInsights['data']['repository'];

        return [
            'basic_info' => [
                'name' => $repo['name'],
                'description' => $repo['description'],
                'url' => $repo['url'],
                'created_at' => $repo['createdAt'],
            ],
            'metrics' => [
                'stars' => $repo['stargazerCount'],
                'forks' => $repo['forkCount'],
                'watchers' => $repo['watchers']['totalCount'],
            ],
            'languages' => array_map(function($lang) {
                return [
                    'name' => $lang['node']['name'],
                    'color' => $lang['node']['color'],
                    'size' => $lang['size'],
                ];
            }, $repo['languages']['edges']),
            'contributors' => array_map(function($contrib) {
                return [
                    'name' => $contrib['node']['name'],
                    'login' => $contrib['node']['login'],
                    'total_commits' => $contrib['contributions']['totalCommitContributions'],
                ];
            }, $repo['contributors']['edges']),
            // Additional transformations...
        ];
    }
}
