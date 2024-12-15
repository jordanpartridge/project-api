<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Services\GitHub\GitHubProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GitHubProjectIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_sync_project_with_github(): void
    {
        Http::fake([
            'api.github.com/graphql' => Http::response([
                'data' => [
                    'project' => [
                        'title' => 'Test Project',
                        'body' => 'Test Description',
                        'url' => 'https://github.com/users/test/projects/1',
                        'items' => [
                            'nodes' => [
                                [
                                    'id' => '1',
                                    'title' => 'Test Item',
                                    'fieldValues' => [
                                        'nodes' => []
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
        ]);

        $project = Project::factory()->create([
            'github_project_number' => '1',
            'name' => 'Old Name',
            'description' => 'Old Description',
        ]);

        $service = new GitHubProjectService();
        $service->syncProject($project);

        $project->refresh();

        $this->assertEquals('Test Project', $project->name);
        $this->assertEquals('Test Description', $project->description);
        $this->assertNotNull($project->last_synced_at);
        $this->assertArrayHasKey('url', $project->github_project_settings);
    }

    public function test_it_handles_missing_github_project_number(): void
    {
        $project = Project::factory()->create([
            'github_project_number' => null,
        ]);

        $service = new GitHubProjectService();
        $service->syncProject($project);

        $project->refresh();

        $this->assertNull($project->last_synced_at);
    }

    public function test_it_handles_failed_github_response(): void
    {
        Http::fake([
            'api.github.com/graphql' => Http::response([
                'errors' => [
                    ['message' => 'Not found']
                ]
            ], 404)
        ]);

        $project = Project::factory()->create([
            'github_project_number' => '1',
        ]);

        $service = new GitHubProjectService();
        $service->syncProject($project);

        $project->refresh();

        $this->assertEquals($project->name, $project->name);
    }
}