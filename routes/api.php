<?php

use App\Http\Resources\LanguageResource;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->as('v1:')->group(function () {
    Route::prefix('users')->as('users:')->group(function () {
        Route::get('/', function (Request $request) {
            return $request->user();
        })->name('authorized');
    });
    Route::prefix('languages')->as('languages:')->group(function () {
        Route::get('/', function (Request $request) {
            return Language::all();
        })->name('index');
        Route::get('{language}', function (Request $request, Language $language) {
            return new LanguageResource($language);
        })->name('show');
        Route::get('{language}/repos', function (Request $request, Language $language) {
            return $language->repos;
        })->name('repos');
    });
});
