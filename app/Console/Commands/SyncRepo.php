<?php

namespace App\Console\Commands;

use App\Events\ProjectCreated;
use App\Models\Language;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JordanPartridge\GithubClient\Data\Repos\RepoData;
use JordanPartridge\GithubClient\Enums\Direction;
use JordanPartridge\GithubClient\Enums\RepoType;
use JordanPartridge\GithubClient\Enums\Sort;
use JordanPartridge\GithubClient\Facades\Github;
use Throwable;

class SyncRepo extends Command
{
    protected $signature = 'repo:sync
        {--limit=100 : The number of repositories to fetch}
        {--display=full : Display mode (full/compact/minimal)}';

    protected $description = 'Synchronize GitHub repositories with local projects';

    public function handle(): void
    {
        $this->components->info('🚀 Initiating GitHub Repository Sync');

        try {
            /** @var Collection<RepoData> $repos */
            $repos = $this->fetchRepositories();

            if ($repos->isEmpty()) {
                $this->components->error('No repositories found matching the criteria.');

                return;
            }

            $this->components->info(sprintf('Found %d repositories to sync...', $repos->count()));

            $progress = $this->output->createProgressBar($repos->count());
            $progress->start();

            /** @var Repo $repo */
            foreach ($repos as $repo) {
                $this->syncRepository($repo, $progress);
                $progress->advance();
            }

            $progress->finish();
            $this->newLine(2);

        } catch (Throwable $e) {
            $this->components->error('Failed to sync repositories: ' . $e->getMessage());

            return;
        }
    }

    /**
     * @return Collection<RepoData>
     */
    private function fetchRepositories(): Collection
    {
        $repos = collect(Github::repos()->all(
            per_page: (int) $this->option('limit'),
            sort: Sort::UPDATED,
            direction: Direction::DESC,
            type: RepoType::Owner,
        )->dto());

        return $repos;
    }

    private function syncRepository(RepoData $repo, $progress): void
    {
        try {
            $this->displayRepositoryInfo($repo);
            $project = $this->createOrUpdateProject($repo);
            $this->syncLanguage($project, $repo->language);

            $this->components->info("✓ Synced {$repo->full_name}");
        } catch (Throwable $e) {
            $this->components->error("Failed to sync {$repo->full_name}: {$e->getMessage()}");
        }
    }

    private function createOrUpdateProject(RepoData $repo): mixed
    {
        $project = ProjectCreated::commit(
            name: Str::title(str_replace('-', ' ', $repo->name)),
            description: $repo->description,
        );

        $project->repo()->updateOrCreate(
            [
                'github_id' => $repo->id,
                'name' => $repo->name,
                'full_name' => $repo->full_name,
            ],
            [
                'description' => $repo->description,
                'url' => $repo->html_url,
                'private' => $repo->private,
                'stars_count' => $repo->stargazers_count,
                'forks_count' => $repo->forks_count,
                'open_issues_count' => $repo->open_issues_count,
                'default_branch' => $repo->default_branch,
                'last_push_at' => $repo->pushed_at,
                'topics' => $repo->topics ?? [],
                'license' => $repo->license?->spdxId ?? null,
            ]
        );

        return $project;
    }

    private function displayRepositoryInfo(RepoData $repo): void
    {
        match ($this->option('display')) {
            'minimal' => $this->displayMinimal($repo),
            'full' => $this->displayFull($repo),
            default => $this->displayCompact($repo),
        };
    }

    private function displayMinimal(RepoData $repo): void
    {
        $this->newLine();
        $this->components->twoColumnDetail(
            "<fg=blue>{$repo->name}</>",
            sprintf(
                '%s | Updated: %s',
                $repo->language ?? 'No Language',
                Carbon::parse($repo->updated_at)->diffForHumans()
            )
        );
    }

    private function displayCompact(RepoData $repo): void
    {
        $this->newLine();
        $this->components->twoColumnDetail('<fg=blue>Repository</>', $repo->full_name);
        $this->components->bulletList([
            "Stars: {$repo->stargazers_count}",
            'Updated: ' . Carbon::parse($repo->updated_at)->diffForHumans(),
            "Issues: {$repo->open_issues_count  }",
        ]);
    }

    private function displayFull(RepoData $repo): void
    {
        $this->newLine();
        $this->components->twoColumnDetail('<fg=blue>Repository</>', $repo->full_name);

        // Basic Info
        $this->components->bulletList([
            '<fg=yellow>Basic Info</>',
            "ID: {$repo->id}",
            "Name: {$repo->name}",
            'Private: ' . ($repo->private ? '🔒 Yes' : '🌐 No'),
            'Description: ' . ($repo->description ?? 'N/A'),
        ]);

        // Stats
        $this->newLine();
        $this->components->bulletList([
            '<fg=yellow>Stats</>',
            "Stars: ⭐ {$repo->stargazers_count}",
            "Forks: 🍴 {$repo->forks_count}",
            "Issues: 🔓 {$repo->open_issues_count}",
            'Size: ' . $this->formatSize($repo->size),
        ]);

        // Dates
        $this->newLine();
        $this->components->bulletList([
            '<fg=yellow>Dates</>',
            'Created: ' . Carbon::parse($repo->created_at)->format('Y-m-d H:i:s'),
            'Updated: ' . Carbon::parse($repo->updated_at)->format('Y-m-d H:i:s'),
            'Pushed: ' . Carbon::parse($repo->pushed_at)->format('Y-m-d H:i:s'),
        ]);
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
}
