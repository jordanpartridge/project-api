<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SaveFailedTests extends Command
{
    protected $signature = 'test:save-failures';
    protected $description = 'Run tests and save failures to .failed-tests file';

    public function handle()
    {
        $output = shell_exec('php artisan test');
        preg_match_all('/FAIL\s+([\w\\\\]+)/', $output, $matches);

        if (! empty($matches[1])) {
            file_put_contents('.failed-tests', implode("\n", $matches[1]));
            $this->info('Failed tests saved to .failed-tests');
        } else {
            unlink('.failed-tests');
            $this->info('All tests passed!');
        }
    }
}
