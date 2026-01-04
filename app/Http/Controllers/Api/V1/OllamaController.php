<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Ollama\OllamaService;
use App\Services\Ollama\OllamaException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OllamaController extends Controller
{
    public function __construct(
        protected OllamaService $ollamaService
    ) {}

    /**
     * Generate a text response using Ollama
     */
    public function generate(Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'prompt' => 'required|string|max:2000',
                'model' => 'nullable|string',
                'options' => 'nullable|array',
                'options.*' => 'numeric'
            ]);

            $response = $this->ollamaService->generate(
                $validated['prompt'],
                $validated['model'] ?? null,
                $validated['options'] ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $response->toArray()
            ]);

        } catch (OllamaException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ollama service error: ' . $e->getMessage()
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Generate code using Ollama
     */
    public function generateCode(Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'prompt' => 'required|string|max:2000',
                'language' => 'required|string',
                'model' => 'nullable|string',
                'options' => 'nullable|array',
                'options.*' => 'numeric'
            ]);

            $response = $this->ollamaService->generateCode(
                $validated['prompt'],
                $validated['language'],
                $validated['model'] ?? null,
                $validated['options'] ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $response->toArray()
            ]);

        } catch (OllamaException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ollama service error: ' . $e->getMessage()
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * List available Ollama models
     */
    public function listModels(): JsonResponse
    {
        try {
            $models = $this->ollamaService->listModels();

            return response()->json([
                'success' => true,
                'data' => $models
            ]);

        } catch (OllamaException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ollama service error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred'
            ], 500);
        }
    }
}