<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\Ollama\OllamaService;

$ollama = new OllamaService();

// Example 1: Basic completion
echo "Example 1: Basic Completion\n";
echo "-------------------------\n";
$response = $ollama->generate("Write a short haiku about coding");
echo $response->response . "\n\n";

// Example 2: Code generation
echo "Example 2: Code Generation\n";
echo "-------------------------\n";
$response = $ollama->generateCode(
    prompt: "Write a PHP function that checks if a number is prime",
    language: "php"
);
echo $response->response . "\n\n";

// Example 3: Technical explanation with custom model
echo "Example 3: Technical Explanation\n";
echo "-------------------------\n";
$response = $ollama->generate(
    prompt: "Explain how garbage collection works in PHP",
    model: "codellama" // Using codellama for technical content
);
echo $response->response . "\n";