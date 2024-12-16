<?php

namespace Tests\Feature;

use App\Services\CodeAnalysis\PrismAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrismAnalyzerTest extends TestCase
{
    protected PrismAnalyzer $analyzer;
    protected string $testFilesPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->analyzer = new PrismAnalyzer(base_path('tests/stubs'));
        $this->testFilesPath = base_path('tests/stubs/code-samples');
        
        if (!is_dir($this->testFilesPath)) {
            mkdir($this->testFilesPath, 0755, true);
        }
    }

    public function test_analyzes_php_file_successfully()
    {
        $sampleCode = '<?php
            function testFunction() {
                return "Hello World";
            }
        ?>';
        
        file_put_contents($this->testFilesPath . '/test.php', $sampleCode);

        $result = $this->analyzer->analyzeFile('code-samples/test.php');

        $this->assertEquals('php', $result['language']);
        $this->assertArrayHasKey('metrics', $result);
        $this->assertArrayHasKey('tokens', $result);
    }

    public function test_handles_unsupported_language()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unsupported language: txt');

        file_put_contents($this->testFilesPath . '/test.txt', 'Plain text file');
        $this->analyzer->analyzeFile('code-samples/test.txt');
    }

    public function test_calculates_metrics_correctly()
    {
        $sampleCode = '<?php
            class TestClass {
                public function test() {
                    echo "Test";
                }
            }
        ?>';
        
        file_put_contents($this->testFilesPath . '/metrics.php', $sampleCode);

        $result = $this->analyzer->analyzeFile('code-samples/metrics.php');
        $metrics = $result['metrics'];

        $this->assertGreaterThan(0, $metrics['lines']);
        $this->assertGreaterThan(0, $metrics['characters']);
        $this->assertGreaterThan(0, $metrics['words']);
    }

    protected function tearDown(): void
    {
        // Cleanup test files
        if (is_dir($this->testFilesPath)) {
            array_map('unlink', glob("$this->testFilesPath/*.*"));
            rmdir($this->testFilesPath);
        }
        
        parent::tearDown();
    }
}