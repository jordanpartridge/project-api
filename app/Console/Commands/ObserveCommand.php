<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ObserveCommand extends Command
{
    protected $signature = 'observe:run';
    protected $description = 'Run observation command';
    
    protected $confidence = 1.0;

    public function handle()
    {
        // Existing handle logic
        $this->info('Observation command executed');
    }
}