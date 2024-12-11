<?php

namespace App\Services\GitHub;

use App\Models\Project;
use Illuminate\Support\Facades\Http;

/**
 * GitHubProjectService handles the integration with GitHub Projects API.
 *
 * This service provides methods to sync project data between the local database
 * and GitHub Projects using GitHub's GraphQL API.
 *
 * @package App\Services\GitHub
 */
class GitHubProjectService
{
    protected string $baseUrl = 'https://api.github.com/graphql';

    /**
     * Synchronize a project with its corresponding GitHub Project.
     *
     * @param Project $project The project to synchronize
     * @throws \Exception When GitHub API call fails
     * @return void
     */
    public function syncProject(Project $project): void
    {
        if (!$project->github_project_number) {
            return;
        }

        $query = $this->buildGraphQLQuery($project->github_project_number);
        $response = $this->executeQuery($query);

        $this->updateProjectFromResponse($project, $response);
    }

    /**
     * Build the GraphQL query for fetching GitHub Project data.
     *
     * @param string $projectNumber The GitHub Project number
     * @return string The GraphQL query
     */
    protected function buildGraphQLQuery(string $projectNumber): string
    {
        return <<<'GRAPHQL'
        query {
          project(number: %s) {
            title
            url
            body
            items(first: 100) {
              nodes {
                id
                title
                fieldValues(first: 8) {
                  nodes {
                    ... on ProjectV2ItemFieldTextValue {
                      text
                      field { name }
                    }
                    ... on ProjectV2ItemFieldDateValue {
                      date
                      field { name }
                    }
                    ... on ProjectV2ItemFieldSingleSelectValue {
                      name
                      field { name }
                    }
                  }
                }
              }
            }
          }
        }
        GRAPHQL;
    }

    /**
     * Execute a GraphQL query against the GitHub API.
     *
     * @param string $query The GraphQL query to execute
     * @throws \Exception When API call fails
     * @return array The API response
     */
    protected function executeQuery(string $query): array
    {
        $response = Http::withToken(config('services.github.token'))
            ->post($this->baseUrl, [
                'query' => $query,
            ]);

        return $response->json();
    }

    /**
     * Update a project with data from the GitHub API response.
     *
     * @param Project $project The project to update
     * @param array $response The GitHub API response
     * @return void
     */
    protected function updateProjectFromResponse(Project $project, array $response): void
    {
        $projectData = $response['data']['project'] ?? null;
        if (!$projectData) {
            return;
        }

        $project->update([
            'name' => $projectData['title'],
            'description' => $projectData['body'],
            'github_project_settings' => [
                'url' => $projectData['url'],
                'items' => $projectData['items']['nodes'] ?? [],
            ],
            'last_synced_at' => now(),
        ]);
    }
}
