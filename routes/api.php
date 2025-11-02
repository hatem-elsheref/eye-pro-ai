<?php


/*
|--------------------------------------------------------------------------
| API Routes for AI Model
|--------------------------------------------------------------------------
| These routes are for the AI model to query match info and update analysis
*/


use Illuminate\Support\Facades\Route;

Route::get('/match/{id}', [\App\Http\Controllers\Api\MatchApiController::class, 'getMatch']);
Route::post('/match/{id}/prediction', [\App\Http\Controllers\Api\MatchApiController::class, 'storePrediction']);
Route::post('/match/{id}/complete', [\App\Http\Controllers\Api\MatchApiController::class, 'completeProcessing']);
