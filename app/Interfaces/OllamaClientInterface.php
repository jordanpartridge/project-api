<?php

namespace App\Interfaces;

interface OllamaClientInterface
{
    /**
     * Generate a text response from the Ollama model
     *
     * @param string $model The name of the model to use
     * @param string $prompt The input prompt
     * @param array $options Additional generation options
     * @return string The generated text response
     * @throws \Exception If generation fails
     */
    public function generate(string $model, string $prompt, array $options = []): string;

    /**
     * Generate a streaming response from the Ollama model
     *
     * @param string $model The name of the model to use
     * @param string $prompt The input prompt
     * @param array $options Additional generation options
     * @return \Generator A generator yielding response chunks
     * @throws \Exception If generation fails
     */
    public function generateStream(string $model, string $prompt, array $options = []): \Generator;

    /**
     * List available Ollama models
     *
     * @return array List of available models with their details
     * @throws \Exception If model listing fails
     */
    public function listModels(): array;

    /**
     * Pull a new model from the Ollama repository
     *
     * @param string $modelName The name of the model to pull
     * @return bool True if successful, false otherwise
     * @throws \Exception If model pulling fails
     */
    public function pullModel(string $modelName): bool;

    /**
     * Check if a specific model is available
     *
     * @param string $modelName The name of the model to check
     * @return bool True if the model is available, false otherwise
     */
    public function hasModel(string $modelName): bool;
}
