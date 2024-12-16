<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can ask about private gpt', function () {
    // Ensure environment variables are set
    expect(env('ANTHROPIC_API_KEY'))->not()->toBeNull('Anthropic API key is not set');
    expect(env('GROQ_API_KEY'))->not()->toBeNull('Groq API key is not set');
    expect(env('OPENAI_API_KEY'))->not()->toBeNull('OpenAI API key is not set');

    // Make a test request about PrivateGPT
    $response = $this->postJson('/api/ask-private-gpt', [
        'query' => 'What is PrivateGPT and how does it work?'
    ]);

    // Assert successful response
    $response->assertStatus(200);
    
    // Check response structure
    $response->assertJsonStructure([
        'query',
        'response'
    ]);

    // Ensure a meaningful response was generated
    $responseData = $response->json();
    expect($responseData['response'])->not()->toBeEmpty('Response should not be empty');
});
