<?php

namespace App\Console\Commands;

use App\Models\Commit;
use App\Models\File;
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
    protected $signature = 'commits:sync {--per-page=100} {--max-pages=100} {--with-files}';
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
            Artisan::call('repo:sync');
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

        // Only process files if the --with-files flag is set
        $this->processFiles($repo);

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

        Commit::upsert($commitData, ['sha'], ['message', 'author', 'committed_at']);
        $this->info(count($commitData) . ' commits upserted');

        return count($commitData);
    }

    private function processFiles(Repo $repo): void
    {
        $this->info('Processing files...');

        // Process files in smaller chunks to avoid memory issues
        Commit::where('repo_id', $repo->id)
            ->whereDoesntHave('files')
            ->chunk(100, function (Collection $commits) {
                $commits->each(function (Commit $commit) {
                    if ($this->apiCalls >= (self::API_RATE_LIMIT - self::RATE_LIMIT_BUFFER)) {
                        $this->error('Approaching API rate limit. Stopping file processing.');

                        return false;
                    }

                    try {
                        $this->info("Processing files for commit {$commit->sha}");
                        $details = Github::commits()->get($commit->repo->full_name, $commit->sha);
                        $this->apiCalls++;

                        if ($details->files) {
                            $fileData = collect($details->files)->map(function ($file) use ($commit) {
                                return [
                                    'commit_id' => $commit->id,
                                    'filename' => $file->filename,
                                    'status' => $file->status,
                                    'additions' => $file->additions,
                                    'deletions' => $file->deletions,
                                    'changes' => $file->changes,
                                    'raw_url' => $file->raw_url ?? null,
                                ];
                            })->all();

                            File::insert($fileData);
                            $this->info(count($fileData) . ' files processed');
                        }

                        $this->enforceRateLimit();

                    } catch (Exception $e) {
                        Log::error("Failed to process files for commit {$commit->sha}: " . $e->getMessage());
                        $this->error("Failed to process files for commit {$commit->sha}: " . $e->getMessage());
                    }
                });
            });
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
