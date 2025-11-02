<?php


/*
|--------------------------------------------------------------------------
| API Routes for AI Model
|--------------------------------------------------------------------------
| These routes are for the AI model to query match info and update analysis
*/


use Illuminate\Support\Facades\Route;

Route::get('/match/{id}', [\App\Http\Controllers\Api\MatchApiController::class, 'getMatch']);
Route::put('/match/{id}/analysis', [\App\Http\Controllers\Api\MatchApiController::class, 'updateAnalysis']);
