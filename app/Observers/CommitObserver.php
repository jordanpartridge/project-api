<?php

namespace App\Observers;

use App\Jobs\ProcessFilesForCommit;
use App\Models\Commit;

class CommitObserver
{
    /**
     * Handle the Commit "created" event.
     */
    public function created(Commit $commit): void
    {
        ProcessFilesForCommit::dispatch($commit);
    }

    /**
     * Handle the Commit "updated" event.
     */
    public function updated(Commit $commit): void
    {
        //
    }

    /**
     * Handle the Commit "deleted" event.
     */
    public function deleted(Commit $commit): void
    {
        //
    }

    /**
     * Handle the Commit "restored" event.
     */
    public function restored(Commit $commit): void
    {
        //
    }

    /**
     * Handle the Commit "force deleted" event.
     */
    public function forceDeleted(Commit $commit): void
    {
        //
    }
}
