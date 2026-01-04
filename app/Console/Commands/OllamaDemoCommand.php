<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OllamaService;

class OllamaDemoCommand extends Command
{
    protected $signature = 'ollama:demo 
        {--model=llama3:8b : The model to use for demonstration}
        {--task=code : The type of task to demonstrate (code|writing|analysis|translation)}';

    protected $description = 'Demonstrate Ollama AI capabilities with different tasks';

    private $tasks = [
        'code' => [
            'prompt' => "Write a PHP Laravel service method to validate user input for a registration form. Include email validation, password strength check, and age verification.",
            'model' => 'codellama:latest'
        ],
        'writing' => [
            'prompt' => "Write a short creative story about an AI that learns empathy by helping people solve their most challenging personal problems.",
            'model' => 'llama3:8b'
        ],
        'analysis' => [
            'prompt' => "Analyze the potential impact of AI on software development workflows in the next 5 years. Discuss both opportunities and challenges.",
            'model' => 'mistral:latest'
        ],
        'translation' => [
            'prompt' => "Translate the following technical paragraph about machine learning from English to Spanish, maintaining technical accuracy: 'Machine learning is a subset of artificial intelligence that focuses on the ability of systems to learn and improve from experience without being explicitly programmed.'",
            'model' => 'llama3:8b'
        ]
    ];

    public function handle(OllamaService $ollamaService)
    {
        // Allow user to override default model or task
        $model = $this->option('model');
        $taskKey = $this->option('task');

        // Validate task
        if (!isset($this->tasks[$taskKey])) {
            $this->error("Invalid task. Choose from: " . implode(', ', array_keys($this->tasks)));
            return 1;
        }

        // Select task details
        $taskDetails = $this->tasks[$taskKey];
        $prompt = $taskDetails['prompt'];
        $selectedModel = $model ?? $taskDetails['model'];

        // Create progress bar
        $this->info("🤖 Ollama AI Demonstration");
        $this->info("Model: $selectedModel");
        $this->info("Task: $taskKey");
        $this->line('----------------------------');

        try {
            // Measure execution time
            $startTime = microtime(true);

            // Generate response
            $response = $ollamaService->generate($selectedModel, $prompt);

            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            // Display response
            $this->line("\n📝 AI Response:");
            $this->line(str_repeat('-', 80));
            $this->info($response);
            $this->line(str_repeat('-', 80));

            // Show metadata
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Model', $selectedModel],
                    ['Task', $taskKey],
                    ['Execution Time', "$executionTime seconds"],
                    ['Response Length', strlen($response) . ' characters']
                ]
            );

            return 0;
        } catch (\Exception $e) {
            $this->error("Error during AI generation: " . $e->getMessage());
            return 1;
        }
    }
}
