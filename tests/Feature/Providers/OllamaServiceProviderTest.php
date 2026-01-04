<?php

namespace Tests\Feature\Providers;

use Tests\TestCase;
use App\Services\Ollama\OllamaService;
use Illuminate\Support\Facades\Config;

class OllamaServiceProviderTest extends TestCase
{
    public function test_ollama_service_is_registered()
    {
        $this->assertTrue($this->app->bound(OllamaService::class));
        
        $service = $this->app->make(OllamaService::class);
        $this->assertInstanceOf(OllamaService::class, $service);
    }

    public function test_ollama_config_is_loaded()
    {
        $this->assertTrue(Config::has('services.ollama'));
        $this->assertIsString(Config::get('services.ollama.url'));
        $this->assertEquals('http://localhost:11434', Config::get('services.ollama.url'));
        $this->assertIsString(Config::get('services.ollama.default_model'));
        $this->assertEquals('llama2', Config::get('services.ollama.default_model'));
    }
}