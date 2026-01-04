<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class ServiceTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configure test services without database setup
        config(['services.ollama.url' => 'http://localhost:11434']);
        config(['services.ollama.cache_duration' => 0]); // Disable caching in tests
        config(['database.default' => 'null']); // Prevent database connections
    }
}