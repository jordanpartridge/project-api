<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenAI\Client;
use Illuminate\Support\Facades\Log;

class AiTroubleshootCommand extends Command
{
    protected $signature = 'ai:troubleshoot {failure? : The test failure details}';
    
    protected $description = 'Analyze test failures using AI and provide solutions';

    public function handle()
    {
        $input = $this->argument('failure') ?? file_get_contents('php://stdin');
        
        if (empty($input)) {
            $this->error('No input provided');
            return 1;
        }

        try {
            $analysis = $this->analyzeFailure($input);
            $this->output->write($analysis);
            return 0;
        } catch (\Exception $e) {
            Log::error('AI Troubleshoot failed', [
                'error' => $e->getMessage(),
                'input' => $input
            ]);
            $this->error($e->getMessage());
            return 1;
        }
    }

    private function analyzeFailure(string $failureDetails): string
    {
        return <<<EOT
## Diagnosis
The test failure indicates {$this->determineFailureType($failureDetails)}

## Solution
1. Review the error message and stack trace
2. Check the specific file and line mentioned
3. Apply the recommended fix based on the error type
4. Run the tests again to verify the solution

## Prevention
- Add appropriate error handling
- Implement proper validation
- Write comprehensive test cases
EOT;
    }

    private function determineFailureType(string $failureDetails): string
    {
        if (str_contains($failureDetails, 'SQLSTATE')) {
            return 'a database connection or query issue';
        } elseif (str_contains($failureDetails, 'TypeError')) {
            return 'a type mismatch in the code';
        } elseif (str_contains($failureDetails, 'ServiceNotFoundException')) {
            return 'a dependency injection configuration problem';
        }
        
        return 'an unknown error that requires investigation';
    }
}
