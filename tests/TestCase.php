<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup fake HTTP responses for Ollama
        Http::fake([
            'localhost:11434/*' => Http::response([
                'model' => 'llama2',
                'response' => 'test response',
                'done' => true
            ], 200)
        ]);
    }
}