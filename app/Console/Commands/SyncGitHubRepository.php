<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GitHub\SyncService;
use Illuminate\Support\Facades\Log;

class SyncGitHubRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'github:sync 
        {repository : Full repository name (owner/repo)} 
        {--force : Force sync even if recent sync exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize a GitHub repository with local database';

    /**
     * GitHub sync service
     *
     * @var SyncService
     */
    protected $syncService;

    /**
     * Create a new command instance.
     *
     * @param SyncService $syncService
     * @return void
     */
    public function __construct(SyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $repositoryName = $this->argument('repository');
        $force = $this->option('force');

        try {
            $this->info("Starting synchronization for repository: {$repositoryName}");

            // Perform full sync
            $repository = $this->syncService->fullSync($repositoryName);

            $this->info("Repository synchronized successfully!");
            $this->table(
                ['Attribute', 'Value'],
                [
                    ['Repository Name', $repository->full_name],
                    ['GitHub ID', $repository->github_id],
                    ['Commits Synced', $repository->commits()->count()],
                    ['Issues Synced', $repository->issues()->count()],
                ]
            );

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Synchronization failed: {$e->getMessage()}");
            Log::error('GitHub Sync Command Failed', [
                'repository' => $repositoryName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return self::FAILURE;
        }
    }
}
