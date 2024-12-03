<?php

namespace App\Jobs;

use App\Models\Commit;
use App\Models\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use JordanPartridge\GithubClient\Facades\Github;

class ProcessFilesForCommit implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Commit $commit) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $details = $this->getCommitDetails();
        // Handle the files data transformation properly
        $files = $details->files;
        // Inside the processFiles method, modify the file attachment part:

        if ($this->shouldProcessFiles($details)) {
            $this->processFiles($files);
        }
    }

    private function getCommitDetails()
    {
        return Github::commits()->get($this->commit->repo->full_name, $this->commit->sha);
    }

    private function shouldProcessFiles($details): bool
    {
        return $details->files ?? false;
    }

    private function processFiles($details): void
    {
        foreach ($details->files as $file) {

            // First create/get the file record
            $fileModel = File::firstOrCreate([
                'filename' => $file->filename,
                'repo_id' => $this->commit->repo->id,
            ]);

            // Then attach the file to the commit with the pivot data
            $this->commit->files()->attach($fileModel->id, [
                'additions' => $file->additions ?? 0,
                'deletions' => $file->deletions ?? 0,
                'changes' => $file->changes ?? 0,
            ]);
        }
    }
}
