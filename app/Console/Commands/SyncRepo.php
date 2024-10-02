<?php

namespace App\Console\Commands;

use App\Events\ProjectCreated;
use App\Http\Integrations\Github\Github as GithubIntegration;
use App\Http\Integrations\Github\Requests\Repos\ListRequest;
use App\Models\Language;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncRepo extends Command
{
    protected $signature = 'sync:repo {--limit=100 : The number of repositories to fetch}';
    protected $description = 'Sync GitHub repository information to projects';

    public function __construct(private readonly GithubIntegration $github)
    {
        parent::__construct();
    }

    public function handle()
    {
        $limit = $this->option('limit');
        $this->info("Fetching up to {$limit} repositories...");

        $request = new ListRequest;
        $request->query()->merge([
            'sort' => 'updated',
            'direction' => 'desc',
            'per_page' => $limit,
            'type' => 'owner',
        ]);

        try {
            $response = $this->github->send($request);
            $repos = $response->json();

            $this->output->progressStart(count($repos));

            foreach ($repos as $repo) {
                $this->syncRepo($repo);
                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
            $this->info('Successfully synced ' . count($repos) . ' repositories.');
        } catch (Exception $e) {
            $this->error('An error occurred while syncing repositories: ' . $e->getMessage());
            Log::error('Repository sync failed', ['error' => $e->getMessage()]);
        }
    }

    private function syncRepo(array $repo)
    {
        $fields = [
            'ID' => $repo['id'],
            'Name' => $repo['name'],
            'Full Name' => $repo['full_name'],
            'Private' => $repo['private'] ? 'Yes' : 'No',
            'Owner' => $repo['owner']['login'],
            'HTML URL' => $repo['html_url'],
            'Description' => $repo['description'] ?? 'N/A',
            'Fork' => $repo['fork'] ? 'Yes' : 'No',
            'Created At' => $repo['created_at'],
            'Updated At' => $repo['updated_at'],
            'Pushed At' => $repo['pushed_at'],
            'Size' => $repo['size'],
            'Stargazers Count' => $repo['stargazers_count'],
            'Watchers Count' => $repo['watchers_count'],
            'Language' => $repo['language'] ?? 'N/A',
            'Has Issues' => $repo['has_issues'] ? 'Yes' : 'No',
            'Has Projects' => $repo['has_projects'] ? 'Yes' : 'No',
            'Has Downloads' => $repo['has_downloads'] ? 'Yes' : 'No',
            'Has Wiki' => $repo['has_wiki'] ? 'Yes' : 'No',
            'Has Pages' => $repo['has_pages'] ? 'Yes' : 'No',
            'Forks Count' => $repo['forks_count'],
            'Open Issues Count' => $repo['open_issues_count'],
            'Default Branch' => $repo['default_branch'],
        ];

        $this->table(['Field', 'Value'], collect($fields)->map(fn ($value, $key) => [$key, $value])->toArray());

        $project = ProjectCreated::commit(
            name: Str::title(str_replace('-', ' ', $repo['name'])),
            description: $repo['description'] ?? null,
        );

        $project->repo()->updateOrCreate([
            'github_id' => $repo['id'],
            'name' => $repo['name'],
            'full_name' => $repo['full_name'],
        ], [
            'description' => $repo['description'] ?? null,
            'url' => $repo['html_url'],
            'private' => $repo['private'],
            'stars_count' => $repo['stargazers_count'],
            'forks_count' => $repo['forks_count'],
            'open_issues_count' => $repo['open_issues_count'],
            'default_branch' => $repo['default_branch'],
            'last_push_at' => $repo['pushed_at'],
            'topics' => $repo['topics'] ?? [],
            'license' => $repo['license']['spdx_id'] ?? null,
        ]);
        //might work?
        if ($repo['language']) {
            $language = Language::firstOrCreate(['name' => $repo['language']]);
            $project->repo->language()->associate($language);
            $project->repo->save();

        }

        $this->info("Project {$project->name} created successfully.");
    }
}
