<?php

use App\Http\Controllers\Api\V1\PrivateGPTController;
use Illuminate\Support\Facades\Route;

Route::post('/query', [PrivateGPTController::class, 'query'])->name('query');
Route::get('/status', [PrivateGPTController::class, 'status'])->name('status');