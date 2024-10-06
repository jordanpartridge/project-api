<?php

namespace App\Console\Commands;

use App\Events\ProjectCreated;
use App\Http\Integrations\Github\Requests\Repos\ListRequest;
use App\Http\Requests\Github as GithubIntegration;
use App\Models\Language;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use function Laravel\Prompts\table;

class SyncRepo extends Command
{
    protected $signature = 'repo:sync
        {--limit=100 : The number of repositories to fetch}
        {--display-mode=full : Display mode for repository info (full/compact)}';

    protected $description = 'Sync GitHub repository information to projects';

    public function __construct(private readonly GithubIntegration $github)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $limit = $this->option('limit');
        $displayMode = $this->option('display-mode');

        $this->info('🚀 Initiating GitHub Repository Sync');
        $this->info("Fetching up to {$limit} repositories...");

        $request = new ListRequest;
        $request->query()->merge([
            'sort' => 'updated',
            'direction' => 'desc',
            'per_page' => $limit,
            'type' => 'owner',
        ]);

        try {
            $this->output->write('Fetching repositories from GitHub... ');
            $response = $this->github->send($request);
            $this->info('Done!');

            $repos = $response->json();

            $progress = $this->output->createProgressBar(count($repos));
            $progress->start();

            foreach ($repos as $repo) {
                $this->syncRepo($repo, $displayMode);
                $progress->advance();
            }

            $progress->finish();
            $this->newLine();
            $this->info('✅ Successfully synced ' . count($repos) . ' repositories.');
        } catch (Exception $e) {
            $this->error('❌ An error occurred while syncing repositories: ' . $e->getMessage());
            Log::error('Repository sync failed', ['error' => $e->getMessage()]);
        }
    }

    private function syncRepo(array $repo, string $displayMode): void
    {
        $fields = $this->prepareRepoFields($repo);

        if ($displayMode === 'full') {
            $this->displayFullRepoInfo($repo['full_name'], $fields);
        } else {
            $this->displayCompactRepoInfo($repo['full_name'], $fields);
        }

        $project = $this->createOrUpdateProject($repo);
        $this->syncLanguage($project, $repo['language'] ?? null);

        $this->info("✨ Project {$project->name} synced successfully.");
    }

    private function prepareRepoFields(array $repo): array
    {
        return [
            ['Field' => 'ID', 'Value' => $repo['id'], 'Category' => 'Basic Info'],
            ['Field' => 'Name', 'Value' => $repo['name'], 'Category' => 'Basic Info'],
            ['Field' => 'Full Name', 'Value' => $repo['full_name'], 'Category' => 'Basic Info'],
            ['Field' => 'Private', 'Value' => $repo['private'] ? '🔒 Yes' : '🌐 No', 'Category' => 'Basic Info'],
            ['Field' => 'Owner', 'Value' => $repo['owner']['login'], 'Category' => 'Basic Info'],
            ['Field' => 'HTML URL', 'Value' => $repo['html_url'], 'Category' => 'Links'],
            ['Field' => 'Description', 'Value' => $repo['description'] ?? 'N/A', 'Category' => 'Details'],
            ['Field' => 'Fork', 'Value' => $repo['fork'] ? '🍴 Yes' : 'No', 'Category' => 'Details'],
            ['Field' => 'Created At', 'Value' => $this->formatDate($repo['created_at']), 'Category' => 'Dates'],
            ['Field' => 'Updated At', 'Value' => $this->formatDate($repo['updated_at']), 'Category' => 'Dates'],
            ['Field' => 'Pushed At', 'Value' => $this->formatDate($repo['pushed_at']), 'Category' => 'Dates'],
            ['Field' => 'Size', 'Value' => $this->formatSize($repo['size']), 'Category' => 'Stats'],
            ['Field' => 'Stargazers', 'Value' => "⭐ {$repo['stargazers_count']}", 'Category' => 'Stats'],
            ['Field' => 'Watchers', 'Value' => "👀 {$repo['watchers_count']}", 'Category' => 'Stats'],
            ['Field' => 'Language', 'Value' => $repo['language'] ?? 'N/A', 'Category' => 'Details'],
            ['Field' => 'Has Issues', 'Value' => $repo['has_issues'] ? '✅ Yes' : '❌ No', 'Category' => 'Features'],
            ['Field' => 'Has Projects', 'Value' => $repo['has_projects'] ? '✅ Yes' : '❌ No', 'Category' => 'Features'],
            ['Field' => 'Has Downloads', 'Value' => $repo['has_downloads'] ? '✅ Yes' : '❌ No', 'Category' => 'Features'],
            ['Field' => 'Has Wiki', 'Value' => $repo['has_wiki'] ? '✅ Yes' : '❌ No', 'Category' => 'Features'],
            ['Field' => 'Has Pages', 'Value' => $repo['has_pages'] ? '✅ Yes' : '❌ No', 'Category' => 'Features'],
            ['Field' => 'Forks', 'Value' => "🍴 {$repo['forks_count']}", 'Category' => 'Stats'],
            ['Field' => 'Open Issues', 'Value' => "🔓 {$repo['open_issues_count']}", 'Category' => 'Stats'],
            ['Field' => 'Default Branch', 'Value' => $repo['default_branch'], 'Category' => 'Details'],
        ];
    }

    private function displayFullRepoInfo(string $repoName, array $fields): void
    {
        $this->info("\n📂 Repository Details: {$repoName}");

        $headers = ['Category', 'Field', 'Value'];
        $rows = collect($fields)->map(function ($field) {
            return [$field['Category'], $field['Field'], $field['Value']];
        })->sortBy(0)->toArray();

        table($headers, $rows);
    }

    private function displayCompactRepoInfo(string $repoName, array $fields): void
    {
        $this->info("\n📂 Repository: {$repoName}");

        $compactFields = array_filter($fields, fn ($field) => in_array($field['Field'], [
            'Name', 'Private', 'Stargazers', 'Forks', 'Language', 'Updated At',
        ]));

        $headers = ['Field', 'Value'];
        $rows = array_map(fn ($field) => [$field['Field'], $field['Value']], $compactFields);

        table($headers, $rows);
    }

    private function createOrUpdateProject(array $repo)
    {
        $project = ProjectCreated::commit(
            name: Str::title(str_replace('-', ' ', $repo['name'])),
            description: $repo['description'] ?? null,
        );

        $project->repo()->updateOrCreate(
            [
                'github_id' => $repo['id'],
                'name' => $repo['name'],
                'full_name' => $repo['full_name'],
            ],
            [
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
            ]
        );

        return $project;
    }

    private function syncLanguage($project, ?string $language): void
    {
        if ($language) {
            $languageModel = Language::firstOrCreate(['name' => $language]);
            $project->repo->language()->associate($languageModel);
            $project->repo->save();
        }
    }

    private function formatSize(int $sizeInKB): string
    {
        $units = ['KB', 'MB', 'GB', 'TB'];
        $size = $sizeInKB;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    private function formatDate(string $date): string
    {
        return Carbon::parse($date)->format('Y-m-d H:i:s');
    }
}
