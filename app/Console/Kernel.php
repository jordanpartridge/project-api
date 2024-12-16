<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\AiTroubleshootCommand;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        AiTroubleshootCommand::class
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    protected function commandsare()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
