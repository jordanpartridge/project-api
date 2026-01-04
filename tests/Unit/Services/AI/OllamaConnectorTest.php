<?php

namespace Tests\Unit\Services\AI;

use Tests\TestCase;
use App\Services\AI\Models\OllamaConnector;
use Illuminate\Support\Facades\Http;
use App\Services\AI\Exceptions\AIModelException;

class OllamaConnectorTest extends TestCase
{
    private OllamaConnector $connector;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connector = new OllamaConnector(
            'http://localhost:11434',
            [
                'default' => 'codellama',
                'analysis' => 'codellama',
            ]
        );
    }

    public function test_analyze_returns_valid_response()
    {
        Http::fake([
            '*/api/analyze' => Http::response([
                'quality_score' => 0.85,
                'issues' => ['Potential memory leak in line 45'],
                'suggestions' => ['Consider using a try-finally block']
            ], 200)
        ]);

        $result = $this->connector->analyze('function test() { /* code */ }');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('quality_score', $result);
        $this->assertArrayHasKey('issues', $result);
        $this->assertArrayHasKey('suggestions', $result);
    }

    public function test_analyze_throws_exception_on_failure()
    {
        Http::fake([
            '*/api/analyze' => Http::response('Internal Server Error', 500)
        ]);

        $this->expectException(AIModelException::class);

        $this->connector->analyze('function test() { /* code */ }');
    }
}