<?php

namespace App\Services\GitHub;

use App\Models\Project;
use Illuminate\Support\Facades\Http;

class GitHubProjectService
{
    protected string $baseUrl = 'https://api.github.com/graphql';

    public function syncProject(Project $project): void
    {
        if (!$project->github_project_number) {
            return;
        }

        $query = $this->buildGraphQLQuery($project->github_project_number);
        $response = $this->executeQuery($query);

        $this->updateProjectFromResponse($project, $response);
    }

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

    protected function executeQuery(string $query): array
    {
        $response = Http::withToken(config('services.github.token'))
            ->post($this->baseUrl, [
                'query' => $query,
            ]);

        return $response->json();
    }

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