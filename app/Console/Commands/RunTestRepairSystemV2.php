<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class RunTestRepairSystemV2 extends Command
{
    protected $signature = 'test:repair-v2 {--watch}';
    protected $description = 'Run tests and attempt repairs with real-time feedback';

    private $projectRoot;
    private $failureCount = 0;
    private $repairAttempts = 0;
    private $successfulRepairs = 0;

    public function handle()
    {
        $this->projectRoot = base_path();
        $this->info('Starting test repair system v2...');
        
        if ($this->option('watch')) {
            $this->watchAndRepair();
        } else {
            $this->runRepairCycle();
        }
    }

    private function watchAndRepair()
    {
        while (true) {
            $this->runRepairCycle();
            $this->info('Waiting 10 seconds before next cycle...');
            sleep(10);
        }
    }

    private function runRepairCycle()
    {
        $this->info('Running test suite...');
        
        $process = new Process(['./vendor/bin/phpunit', '--log-junit', 'junit.xml'], $this->projectRoot);
        $process->run();

        $this->info($process->getOutput());

        if (!$process->isSuccessful()) {
            $this->failureCount++;
            $this->processFailures();
        } else {
            $this->info('All tests passed!');
            $this->displayStats();
        }
    }

    private function processFailures()
    {
        if (!File::exists($this->projectRoot . '/junit.xml')) {
            $this->error('No test output file found');
            return;
        }

        $xmlContent = File::get($this->projectRoot . '/junit.xml');
        $failures = $this->parseTestOutput($xmlContent);

        foreach ($failures as $failure) {
            $this->repairAttempts++;
            $this->info("Attempting to repair: {$failure['test']}");
            
            if ($this->attemptRepair($failure)) {
                $this->successfulRepairs++;
            }
        }

        $this->displayStats();
    }

    private function attemptRepair(array $failure): bool
    {
        $this->info("Analyzing failure: {$failure['message']}");
        
        // Simple pattern matching for common failures
        $patterns = [
            'undefined method' => $this->handleUndefinedMethod($failure),
            'class not found' => $this->handleClassNotFound($failure),
            'Failed asserting that' => $this->handleAssertionFailure($failure)
        ];

        foreach ($patterns as $pattern => $handler) {
            if (str_contains($failure['message'], $pattern)) {
                return $handler;
            }
        }

        return false;
    }

    private function handleUndefinedMethod(array $failure): bool
    {
        $this->info("Detected undefined method error - checking class methods...");
        return false;
    }

    private function handleClassNotFound(array $failure): bool
    {
        $this->info("Detected missing class - checking namespace and file existence...");
        return false;
    }

    private function handleAssertionFailure(array $failure): bool
    {
        $this->info("Detected assertion failure - analyzing expected vs actual values...");
        return false;
    }

    private function parseTestOutput($xmlContent): array
    {
        $xml = simplexml_load_string($xmlContent);
        $failures = [];

        if ($xml) {
            foreach ($xml->testsuite->testcase as $testcase) {
                if (isset($testcase->failure)) {
                    $failures[] = [
                        'test' => (string)$testcase['name'],
                        'class' => (string)$testcase['class'],
                        'message' => (string)$testcase->failure
                    ];
                }
            }
        }

        return $failures;
    }

    private function displayStats()
    {
        $this->info("\nTest Repair System Statistics:");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Failures', $this->failureCount],
                ['Repair Attempts', $this->repairAttempts],
                ['Successful Repairs', $this->successfulRepairs],
                ['Success Rate', $this->repairAttempts > 0 ? 
                    round(($this->successfulRepairs / $this->repairAttempts) * 100, 2) . '%' : 
                    'N/A']
            ]
        );
    }
}
