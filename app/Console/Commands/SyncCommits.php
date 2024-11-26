<?php

namespace App\Console\Commands;

use App\Models\Repo;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use JordanPartridge\GithubClient\Facades\Github;

class SyncCommits extends Command
{
    protected $signature = 'commits:sync';
    protected $description = 'Sync commits from Github';

    public function handle(): void
    {
        $this->syncReposIfNeeded();
        $this->info('Syncing commits...');

        Repo::chunk(100, function (Collection $repos) {
            $repos->each(fn (Repo $repo) => $this->syncRepoCommits($repo));
        });
    }

    private function syncRepoCommits(Repo $repo): void
    {
        $this->info("Syncing commits for {$repo->full_name}");

        try {
            $commits = Github::commits()->all($repo->full_name);
            $this->processCommits($commits->json(), $repo);
        } catch (Exception $e) {
            $this->handleError($e, "Error syncing commits for {$repo->full_name}");
        }
    }

    private function processCommits(array $commits, Repo $repo): void
    {
        collect($commits)->each(function ($commit) use ($repo) {
            if (is_string($commit)) {
                $this->error("{$commit} for {$repo->full_name}");

                return;
            }

            try {
                $files = $this->fetchCommitFiles($commit['url']);
                $this->processFiles($files, $repo);
                $this->createOrUpdateCommit($commit, $repo);
            } catch (Exception $e) {
                $this->error("{$e->getMessage()} for {$repo->full_name}");
            }
        });
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    private function fetchCommitFiles(string $url): array
    {
        try {
            $response = Http::timeout(15)
                ->retry(3, 100)
                ->get($url);

            if (! $response->successful()) {
                throw new RequestException($response);
            }

            $files = $response->json('files');

            if (! is_array($files)) {
                throw new InvalidArgumentException(
                    'GitHub API returned invalid files format. Expected array, got ' . gettype($files)
                );
            }

            return $files;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('GitHub API connection failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function createOrUpdateCommit(array $commit, Repo $repo): void
    {
        $repo->commits()->updateOrCreate(
            ['sha' => $commit['sha']],
            [
                'message' => $commit['commit']['message'],
                'author' => $commit['commit']['committer']['name'],
                'committed_at' => $commit['commit']['author']['date'],
            ]
        );
    }

    private function processFiles(array $files, Repo $repo): void
    {
        $this->info('Processing files...');

        collect($files)->each(function ($file) use ($repo) {
            $this->info("Processing file: {$file['filename']}");

            $created = $repo->files()->updateOrCreate(
                ['path' => $file['filename']],
                [
                    'content' => $file['contents_url'],
                    'raw_url' => $file['raw_url'],
                    'sha' => $file['sha'],
                ]
            );

            $this->info("File created: {$created->path}");
        });
    }

    private function syncReposIfNeeded(): void
    {
        if (Repo::count() === 0) {
            $this->info('No repos found. Syncing repos first...');
            Artisan::call('repo:sync');
        }
    }

    private function handleError(Exception $e, string $context): void
    {
        report($e);
        report(new Exception($context));
        $this->error($e->getMessage());
    }
}
