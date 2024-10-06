<?php

use App\Http\Controllers\Languages\IndexController;
use App\Http\Controllers\Languages\ShowController;
use App\Http\Controllers\Repos\IndexController as RepoIndexController;

Route::get('/', IndexController::class)->name('index');
Route::get('{language}', ShowController::class)->name('show');
Route::prefix('{?language}/repos')->as('repos:')->group(function () {
    Route::get('/', RepoIndexController::class)->name('index');
});
