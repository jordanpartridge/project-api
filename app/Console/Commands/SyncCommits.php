<?php

namespace App\Console\Commands;

use App\Models\Repo;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use JordanPartridge\GithubClient\Facades\Github;

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
    public function handle(): void
    {
        $this->syncReposIfNeeded();
        $this->info('Syncing commits...');
        $repos = Repo::all();
        $repos->each(function (Repo $repo) {
            $this->info('Syncing commits for ' . $repo->full_name);

            try {
                $response = Github::commits()->all($repo->full_name);

                collect($response->json())->each(function ($commit) use ($repo) {
                    $files = Http::get($commit['url'])->json('files');
                    $this->processFiles($files, $repo);
                    if (is_string($commit)) {
                        $this->error($commit . ' for ' . $repo->full_name);

                        return;
                    }
                    try {
                        $repo->commits()->updateOrCreate([
                            'sha' => $commit['sha'],
                        ], [
                            'message' => $commit['commit']['message'],
                            'author' => $commit['commit']['committer']['name'],
                            'committed_at' => $commit['commit']['author']['date'],
                        ]);
                    } catch (Exception $e) {
                        $this->error($e->getMessage() . ' for ' . $repo->full_name);
                    }

                });
            } catch (Exception $e) {
                report($e);
                report(new Exception('Error syncing commits for ' . $repo->full_name));
                $this->error($e->getMessage());
            }

        });
    }

    private function syncReposIfNeeded(): void
    {
        if (Repo::count() === 0) {
            $this->info('No repos found. Syncing repos first...');
            Artisan::call('repo:sync');
        }
    }

    private function processFiles(mixed $files, Repo $repo): void
    {
        $this->info('Processing files...');
        collect($files)->each(function ($file) use ($repo) {
            $this->info('Processing file: ' . $file['filename']);
            $created = $repo->files()->updateOrCreate([
                'path' => $file['filename'],
            ], [
                'content' => $file['contents_url'],
                'raw_url' => $file['raw_url'],
                'sha' => $file['sha'],

            ]);
            $this->info('File created: ' . $created->path);
        });
    }
}
