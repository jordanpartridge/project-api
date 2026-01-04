<?php

namespace App\Http\Controllers;

use App\Services\Ollama\OllamaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIAssistantController extends Controller
{
    protected OllamaService $ollama;
    
    public function __construct(OllamaService $ollama)
    {
        $this->ollama = $ollama;
    }
    
    /**
     * Generate a response to a user's query
     */
    public function chat(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:1000',
            'context' => 'nullable|string',
            'model' => 'nullable|string'
        ]);
        
        try {
            // Build prompt with context if provided
            $prompt = $request->context 
                ? "Context:\n{$request->context}\n\nQuery: {$request->query}"
                : $request->query;
            
            $response = $this->ollama->generate(
                prompt: $prompt,
                model: $request->model
            );
            
            return response()->json([
                'success' => true,
                'response' => $response->response,
                'model' => $response->model,
                'cached' => $response->createdAt->diffInSeconds(now()) > 1
            ]);
        } catch (\Exception $e) {
            Log::error('AI Assistant failed', [
                'query' => $request->query,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate response'
            ], 500);
        }
    }
    
    /**
     * Generate code based on a description
     */
    public function generateCode(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'language' => 'required|string'
        ]);
        
        try {
            $response = $this->ollama->generateCode(
                prompt: $request->prompt,
                language: $request->language
            );
            
            return response()->json([
                'success' => true,
                'code' => $response->response,
                'language' => $request->language
            ]);
        } catch (\Exception $e) {
            Log::error('Code generation failed', [
                'prompt' => $request->prompt,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate code'
            ], 500);
        }
    }
}