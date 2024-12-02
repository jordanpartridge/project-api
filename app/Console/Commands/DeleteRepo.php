<?php

namespace App\Console\Commands;

use App\Models\Repo;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;

class DeleteRepo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repo:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(\JordanPartridge\GithubClient\Github $github): void
    {
        $repos = Repo::all();
        $select = select('repo', $repos->pluck('full_name', 'id')->toArray());
        $confirm = $this->confirm('Are you sure you want to delete this repo?');
        if ($confirm) {
            $repo = Repo::find($select);
            $response = $github->deleteRepo($repo);
            if ($response->successful()) {
                $repo->delete();

                $this->info('Repo deleted successfully');
            } else {
                $this->error('Something went wrong');
            }
        }
    }
}
