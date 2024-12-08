<?php

namespace App\Console\Commands;

use App\Jobs\ProcessFilesForCommit;
use App\Models\Commit;
use App\Models\Repo;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use JordanPartridge\GithubClient\Facades\Github;
use Psy\Readline\Hoa\Exception;

class SyncCommits extends Command
{
    private const API_RATE_LIMIT = 5000; // GitHub's rate limit per hour
    private const RATE_LIMIT_BUFFER = 100; // Buffer to prevent hitting the limit
    protected $signature = 'sync:commits {--per-page=100} {--max-pages=100} {--with-files}';
    protected $description = 'Sync commits from Github with pagination support';

    // Track API calls for rate limiting
    private int $apiCalls = 0;

    public function handle(): void
    {
        $this->ensureReposExist();
        $this->syncAllRepos();
    }

    private function ensureReposExist(): void
    {
        if (Repo::count() === 0) {
            $this->info('No repos found. Syncing repos first...');
            Artisan::call('sync:repos');
        }
    }

    private function syncAllRepos(): void
    {
        $this->info('Syncing commits...');

        Repo::chunk(100, function (Collection $repos) {
            $repos->each(function (Repo $repo) {
                if ($this->apiCalls >= (self::API_RATE_LIMIT - self::RATE_LIMIT_BUFFER)) {
                    $this->error('Approaching API rate limit. Stopping sync.');

                    return false;
                }

                $this->syncRepoCommits($repo);
            });
        });
    }

    private function syncRepoCommits(Repo $repo): void
    {
        $this->info("Syncing commits for {$repo->full_name}");

        $perPage = $this->option('per-page');
        $maxPages = $this->option('max-pages');
        $page = 1;
        $processedCommits = 0;

        do {
            $this->info("Fetching page {$page}...");

            try {
                $commits = Github::commits()->all(
                    repo_name: $repo->full_name,
                    per_page: $perPage,
                    page: $page
                );
                $this->apiCalls++;

                if (empty($commits)) {
                    break;
                }

                $processedCommits += $this->processCommits($repo, $commits);

                if ($page >= $maxPages) {
                    $this->warn("Reached maximum page limit ({$maxPages}) for {$repo->full_name}");
                    break;
                }

                $page++;
                $this->enforceRateLimit();

            } catch (Exception $e) {
                Log::error("Failed to sync commits for {$repo->full_name}: " . $e->getMessage());
                $this->error("Failed to sync commits for {$repo->full_name}: " . $e->getMessage());

                return;
            }

        } while (! empty($commits));

        $this->info("Processed {$processedCommits} commits for {$repo->full_name}");

    }

    private function processCommits(Repo $repo, array $commits): int
    {
        $this->info('Processing commits...');

        $commitData = collect($commits)->map(function ($commit) use ($repo) {
            return [
                'sha' => $commit->sha,
                'message' => $commit->commit->message,
                'author' => $commit->commit->author->toJson(),
                'committed_at' => $commit->commit->author->date,
                'repo_id' => $repo->id,
            ];
        })->all();

        $count = Commit::upsert($commitData, ['sha'], ['message', 'author', 'committed_at']);
        $this->info($count . ' commits upserted');
        Commit::whereIn('sha', array_column($commitData, 'sha'))
            ->get()
            ->each(fn ($commit) => ProcessFilesForCommit::dispatch($commit));

        return count($commitData);
    }

    private function shouldStopDueToRateLimit(): bool
    {
        return $this->apiCalls >= (self::API_RATE_LIMIT - self::RATE_LIMIT_BUFFER);
    }

    private function enforceRateLimit(): void
    {
        if ($this->apiCalls % 100 === 0) {
            $this->info("Made {$this->apiCalls} API calls. Sleeping for 2 seconds...");
            sleep(2);
        } else {
            usleep(100000); // 100ms delay between regular calls
        }
    }
}
