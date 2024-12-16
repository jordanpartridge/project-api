<?php

namespace Tests\Feature\Services\PrivateGPT;

use App\Models\PrivateGPTAnalysis;
use App\Services\PrivateGPT\PrivateGPTService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivateGPTServiceTest extends TestCase
{
    use RefreshDatabase;

    private PrivateGPTService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PrivateGPTService::class);
    }

    public function test_it_can_create_analysis(): void
    {
        $prompt = 'Test prompt';
        $analysis = $this->service->analyze($prompt);

        $this->assertInstanceOf(PrivateGPTAnalysis::class, $analysis);
        $this->assertEquals($prompt, $analysis->prompt);
        $this->assertNotNull($analysis->response);
    }

    public function test_it_caches_analysis(): void
    {
        $prompt = 'Cache test prompt';
        
        $firstAnalysis = $this->service->getCachedAnalysis($prompt);
        $secondAnalysis = $this->service->getCachedAnalysis($prompt);

        $this->assertEquals($firstAnalysis->id, $secondAnalysis->id);
    }
}
