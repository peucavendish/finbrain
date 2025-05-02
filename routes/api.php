<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SimulationController;
use App\Http\Controllers\API\SeguroVidaController;
use App\Http\Controllers\AIAnalysisController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/diagnostico-seguro', [SeguroVidaController::class, 'diagnostico']);

// AI Analysis routes
Route::post('/ai-analysis', [AIAnalysisController::class, 'analyze']);
Route::get('/ai-analysis/history', [AIAnalysisController::class, 'getAnalysisHistory']);

Route::middleware('auth:sanctum')->group(function () {
    // Simulation API routes
    Route::prefix('simulations')->name('api.simulations.')->group(function () {
        Route::post('/retirement', [SimulationController::class, 'calculateRetirement'])->name('retirement');
        Route::post('/passive-income', [SimulationController::class, 'calculatePassiveIncome'])->name('passive-income');
        Route::post('/comparison', [SimulationController::class, 'compareInvestments'])->name('comparison');
    });

    // User profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
}); 