<?php

namespace App\Console\Commands;

use App\Models\Repo;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SyncCommits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commits:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync commits from Github';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\Github $github): void
    {
        $this->syncReposIfNeeded();
        $this->info('Syncing commits...');
        $repos = Repo::all();
        $repos->each(function (Repo $repo) use ($github) {
            $this->info('Syncing commits for ' . $repo->full_name);

            try {
                $response = $github->commits($repo);
            } catch (Exception $e) {
                report($e);
                report(new Exception('Error syncing commits for ' . $repo->full_name));
                $this->error($e->getMessage());
            }
            $this->error($e->getMessage());
            collect($response->json())->each(function ($commit) use ($repo) {
                if (is_string($commit)) {
                    $this->error($commit . ' for ' . $repo->full_name);

                    return;
                }
                try {
                    $repo->commits()->updateOrCreate([
                        'sha' => $commit['sha'],
                    ], [
                        'message' => $commit['commit']['message'],
                        'author' => $commit['commit']['author']['name'],
                        'date' => $commit['commit']['author']['date'],
                    ]);
                } catch (Exception $e) {
                    $this->error($e->getMessage() . ' for ' . $repo->full_name);
                }

            });
        });
    }

    private function syncReposIfNeeded(): void
    {
        if (Repo::count() === 0) {
            $this->info('No repos found. Syncing repos first...');
            Artisan::call('repo:sync');
        }
    }
}
