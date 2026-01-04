<?php

use App\Http\Controllers\Api\V1\OllamaController;
use Illuminate\Support\Facades\Route;

Route::prefix('ollama')->group(function () {
    Route::post('/generate', [OllamaController::class, 'generate']);
    Route::post('/generate/code', [OllamaController::class, 'generateCode']);
    Route::get('/models', [OllamaController::class, 'listModels']);
});