<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AIAssistantController extends Controller
{
    public function askAboutPrivateGPT(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid query',
                'details' => $validator->errors()
            ], 400);
        }

        try {
            $response = $this->generateAIResponse($request->input('query'));
            
            return response()->json([
                'query' => $request->input('query'),
                'response' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Query processing failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    private function generateAIResponse(string $query): string
    {
        // Placeholder AI response generation
        return "AI response to: $query";
    }
}
