<?php

namespace Tests\Unit\Services\AI;

use Tests\TestCase;
use App\Services\AI\OllamaOrchestrator;
use Illuminate\Support\Facades\Http;

class OllamaOrchestratorTest extends TestCase
{
    private OllamaOrchestrator $orchestrator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orchestrator = new OllamaOrchestrator();
    }

    public function test_generates_valid_code()
    {
        Http::fake([
            '*/api/generate' => Http::response([
                'response' => '<?php

namespace App\Services\Example;

/**
 * Example service class
 */
class ExampleService
{
    public function process(): string
    {
        return "processed";
    }
}'
            ])
        ]);

        $result = $this->orchestrator->generateCode(
            'Create a simple service class called ExampleService',
            ['namespace' => 'App\Services\Example']
        );

        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('validation', $result);
        $this->assertTrue($result['validation']['isValid']);
    }

    public function test_analyzes_code()
    {
        Http::fake([
            '*/api/generate' => Http::response([
                'response' => 'Code Analysis Results:
- Follows PSR-12 standards
- Has proper type hints
- Includes PHPDoc comments'
            ])
        ]);

        $code = '<?php
namespace App\Services;

class TestService
{
    public function test(): void
    {
        echo "test";
    }
}';

        $result = $this->orchestrator->analyzeCode($code);

        $this->assertArrayHasKey('analysis', $result);
        $this->assertNotEmpty($result['analysis']);
    }

    public function test_reviews_code()
    {
        Http::fake([
            '*/api/generate' => Http::response([
                'response' => 'Code Review Results:
- No security vulnerabilities found
- Follows Laravel best practices
- Good test coverage'
            ])
        ]);

        $code = '<?php
namespace App\Services;

class TestService
{
    public function test(): void
    {
        echo "test";
    }
}';

        $result = $this->orchestrator->reviewCode($code, ['security' => true, 'best_practices' => true]);

        $this->assertArrayHasKey('review', $result);
        $this->assertArrayHasKey('standards_checked', $result);
    }

    public function test_handles_invalid_code_generation()
    {
        Http::fake([
            '*/api/generate' => Http::response([
                'response' => 'function test() { echo "test"; }'
            ])
        ]);

        $result = $this->orchestrator->generateCode('Create a simple function');

        $this->assertArrayHasKey('validation', $result);
        $this->assertFalse($result['validation']['isValid']);
        $this->assertNotEmpty($result['validation']['issues']);
    }
}