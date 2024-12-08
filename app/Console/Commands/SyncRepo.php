<?php

namespace App\Console\Commands;

use App\Events\ProjectCreated;
use App\Models\Language;
use App\Models\Owner;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JordanPartridge\GithubClient\Data\Repos\RepoData;
use JordanPartridge\GithubClient\Enums\Direction;
use JordanPartridge\GithubClient\Enums\Sort;
use JordanPartridge\GithubClient\Facades\Github;
use Throwable;

use function Laravel\Prompts\error;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\warning;

class SyncRepo extends Command
{
    protected $signature = 'sync:repos
        {--display=full : Display mode (full/compact/minimal)}';

    protected $description = 'Synchronize GitHub repositories with local projects';

    public function handle(): void
    {
        info('🚀 Starting GitHub Repository Sync');

        try {
            $repos = $this->fetchAllRepositories();

            if ($repos->isEmpty()) {
                warning('No repositories found matching the criteria.');

                return;
            }

            info(sprintf('Found %d repositories to sync...', $repos->count()));

            $progress = progress('Syncing repositories', $repos->count());

            foreach ($repos as $index => $repo) {
                $progress->advance(
                    step: $index + 1
                );

                $this->syncRepository($repo);
            }

            $progress->finish();
            info('Sync completed successfully!');

        } catch (Throwable $e) {
            warning('Failed to sync repositories: ' . $e->getMessage());

            return;
        }
    }

    /**
     * Fetch all repositories using pagination
     *
     * @return Collection<RepoData>
     */
    private function fetchAllRepositories(): Collection
    {
        $allRepos = collect();
        $page = 1;
        $perPage = 100;

        while (true) {

            $repos = collect(Github::repos()->all(
                page: $page,
                per_page: $perPage,
                sort: Sort::UPDATED,
                direction: Direction::DESC,
            )->dto());

            if ($repos->isEmpty()) {
                break;
            }

            $allRepos = $allRepos->merge($repos);
            $page++;
        }

        return $allRepos;
    }

    private function syncRepository(RepoData $repo): void
    {
        try {
            $this->displayRepositoryInfo($repo);
            $project = $this->createOrUpdateProject($repo);
            $this->syncLanguage($project, $repo->language);

        } catch (Throwable $e) {
            error("Failed to sync {$repo->full_name}: {$e->getMessage()}");
        }
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
        $this->line(sprintf(
            '  <fg=blue>%s</> %s (Updated: %s)',
            $repo->name,
            $repo->language ?? 'No Language',
            $this->formatDate($repo->updated_at)
        ));
    }

    private function displayCompact(RepoData $repo): void
    {
        $this->line(sprintf(
            "  <fg=blue>%s</>\n  ⭐ %d | 📅 %s | 🔓 %d issues",
            $repo->full_name,
            $repo->stargazers_count,
            $this->formatDate($repo->updated_at),
            $repo->open_issues_count
        ));
    }

    private function displayFull(RepoData $repo): void
    {
        $this->newLine();
        $this->line(sprintf('  <fg=blue;options=bold>%s</>', $repo->full_name));
        $this->line(sprintf('  %s', $repo->private ? '🔒 Private' : '🌐 Public'));

        if ($repo->description) {
            $this->line(sprintf('  %s', $repo->description));
        }

        $this->line(sprintf(
            '  ⭐ %d stars | 🍴 %d forks | 🔓 %d issues | 📦 %s',
            $repo->stargazers_count,
            $repo->forks_count,
            $repo->open_issues_count,
            $this->formatSize($repo->size)
        ));

        $this->line(sprintf(
            '  Created: %s | Updated: %s',
            $this->formatDate($repo->created_at),
            $this->formatDate($repo->updated_at)
        ));

        if ($repo->language) {
            $this->line(sprintf('  Language: %s', $repo->language));
        }

        if (! empty($repo->topics)) {
            $this->line(sprintf('  Topics: %s', implode(', ', $repo->topics)));
        }

        $this->newLine();
    }

    private function createOrUpdateProject(RepoData $repo): mixed
    {
        $project = ProjectCreated::commit(
            name: Str::title(str_replace('-', ' ', $repo->name)),
            description: $repo->description,
        );

        $owner = Owner::updateOrCreate(
            ['login' => $repo->owner->login],
            [
                'avatar_url' => $repo->owner->avatar_url,
                'type' => $repo->owner->type,
                'html_url' => $repo->owner->html_url, X,
            ]
        );
        $project->repo()->updateOrCreate(
            [
                'github_id' => $repo->id,
                'name' => $repo->name,
                'full_name' => $repo->full_name,
                'owner_id' => $owner->id,
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
        return Carbon::parse($date)->diffForHumans();
    }
}
