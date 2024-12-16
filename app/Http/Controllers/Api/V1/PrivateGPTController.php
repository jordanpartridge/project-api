<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class PrivateGPTController extends Controller
{
    public function query(Request $request)
    {
        try {
            $pythonScript = base_path('private-gpt/query.py');
            $process = new Process([
                'python',
                $pythonScript,
                '--query',
                $request->input('query')
            ]);

            $process->run();

            if (!$process->isSuccessful()) {
                return response()->json([
                    'error' => 'Failed to process query',
                    'details' => $process->getErrorOutput()
                ], 500);
            }

            return response()->json([
                'response' => $process->getOutput()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function status()
    {
        try {
            $pythonScript = base_path('private-gpt/verify_gpu.py');
            $process = new Process(['python', $pythonScript]);
            $process->run();

            return response()->json([
                'status' => $process->isSuccessful() ? 'ok' : 'error',
                'details' => $process->getOutput(),
                'error' => $process->getErrorOutput()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}