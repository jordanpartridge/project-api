<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->as('v1:')->group(function () {
    Route::prefix('users')->as('users:')->group(function () {
        Route::get('/', function (Request $request) {
            return $request->user();
        })->name('authorized');
    });
    Route::prefix('languages')->as('languages:')->group(base_path('routes/api/v1/languages.php'));

});
