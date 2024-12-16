<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class AITroubleshooterTest extends TestCase
{
    /** @test */
    public function it_can_handle_basic_test_failure_scenarios()
    {
        $testScenarios = [
            'database_connection_error' => 
                "Database connection failed\n" .
                "Error: SQLSTATE[HY000] [2002] Connection refused",

            'type_mismatch_error' => 
                "TypeError: Argument 1 passed to UserService::createUser() must be of type string, null given",

            'dependency_injection_failure' =>
                "Symfony\\Component\\DependencyInjection\\Exception\\ServiceNotFoundException\n" .
                "Argument 2 of UserService::__construct() has no defined value."
        ];

        foreach ($testScenarios as $scenarioName => $failureDetails) {
            $exitCode = Artisan::call('ai:troubleshoot', [
                'failure' => $failureDetails
            ]);

            $output = Artisan::output();

            $this->assertEquals(0, $exitCode, "Command failed for scenario: $scenarioName");
            $this->assertNotEmpty($output, "AI troubleshooter failed for scenario: $scenarioName");
            $this->assertStringContainsString('## Diagnosis', $output, "Diagnosis section missing for scenario: $scenarioName");
            $this->assertStringContainsString('## Solution', $output, "Solution section missing for scenario: $scenarioName");
        }
    }
}
