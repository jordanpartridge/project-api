#!/usr/bin/env php
<?php

namespace OllamaMCP;

class InferenceController {
    private $logFile;

    public function __construct() {
        $this->logFile = '/tmp/ollama_inference_test.log';
    }

    private function log($message) {
        file_put_contents($this->logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
        echo $message . PHP_EOL;
    }

    private function runShellCommand($command) {
        $this->log("Executing command: $command");
        
        $descriptorspec = [
            0 => ["pipe", "r"],  // stdin
            1 => ["pipe", "w"],  // stdout
            2 => ["pipe", "w"]   // stderr
        ];

        $process = proc_open($command, $descriptorspec, $pipes);

        if (!is_resource($process)) {
            $this->log("Failed to execute command");
            return ['output' => [], 'error' => 'Command execution failed', 'return' => -1];
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $returnVar = proc_close($process);

        $this->log("Command stdout: " . $stdout);
        $this->log("Command stderr: " . $stderr);
        $this->log("Return value: $returnVar");

        return [
            'output' => explode("\n", $stdout),
            'error' => $stderr,
            'return' => $returnVar
        ];
    }

    public function performSystemDiagnostics() {
        $diagnostics = [];

        // Check PHP CLI availability
        $diagnostics['php_cli'] = $this->runShellCommand('which php');

        // Check Ollama installation
        $diagnostics['ollama_which'] = $this->runShellCommand('which ollama');

        // Check Ollama server status
        $diagnostics['ollama_server'] = $this->runShellCommand('curl -s http://localhost:11434/api/version');

        // List available models
        $diagnostics['ollama_models'] = $this->runShellCommand('ollama list');

        return $diagnostics;
    }

    public function analyzeErrors($diagnostics) {
        // Prepare a clean, safe error context
        $errorContext = "Diagnostic analysis for Ollama integration:\n";
        
        foreach ($diagnostics as $key => $result) {
            $errorContext .= "--- $key ---\n";
            $errorContext .= "Output: " . implode("\n", $result['output']) . "\n";
            $errorContext .= "Error: " . $result['error'] . "\n";
            $errorContext .= "Return Value: " . $result['return'] . "\n\n";
        }

        // Use a more robust JSON encoding
        $safeErrorContext = json_encode([
            'prompt' => $errorContext,
            'model' => 'llama3:8b',
            'stream' => false
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Construct a safer curl command
        $curlCommand = sprintf(
            'curl -s -X POST http://localhost:11434/api/generate -H "Content-Type: application/json" -d %s',
            escapeshellarg($safeErrorContext)
        );

        $this->log("Sending error context to Ollama for analysis");
        
        $analysisResult = $this->runShellCommand($curlCommand);

        return $analysisResult;
    }
}

// Main execution
$controller = new InferenceController();

try {
    $diagnostics = $controller->performSystemDiagnostics();
    $analysisResults = $controller->analyzeErrors($diagnostics);
    
    // Combine analysis into a single string for notification
    $analysisMessage = implode(" ", $analysisResults['output']);
    
    // Truncate if too long
    $analysisMessage = substr($analysisMessage, 0, 500);
    
    // Optional: Pipe to say for notification
    exec("echo \"" . escapeshellarg($analysisMessage) . "\" | say");
} catch (\Exception $e) {
    $controller->log("Unexpected error: " . $e->getMessage());
}
