<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ListPulls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list:pulls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List pull requests';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //type-ahead for repos
        $repo = $this->ask('Enter the repository name (e.g., jordanpartridge/github-client):');

        \JordanPartridge\GithubClient\Facades\Github::pulls($repo)->list();
    }
}
