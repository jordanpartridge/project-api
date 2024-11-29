<?php

namespace App\Console\Commands;

use App\Models\Repo;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use JordanPartridge\GithubClient\Facades\Github;

class SyncCommits extends Command
{
    protected $signature = 'commits:sync';
    protected $description = 'Sync commits from Github';

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
            $repos->each(fn (Repo $repo) => $this->syncRepoCommits($repo));
        });
    }

    private function syncRepoCommits(Repo $repo): void
    {
        $this->info("Syncing commits for {$repo->full_name}");

        $commits = Github::commits()->all($repo->full_name);
        collect($commits)->each(function ($commit) use ($repo) {
            $commit = $commit->toArray();
            $commit['message'] = $commit['commit']['message'];
            $repo->commits()->create($commit);
        });

    }
}
