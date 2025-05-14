<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SimulationController;
use App\Http\Controllers\AIAnalysisController;
use App\Http\Controllers\ViverDeRendaIAController;
use App\Http\Controllers\DiagnosticoSeguroController;
use App\Http\Controllers\DiagnosticoCarteiraController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rotas públicas
Route::middleware(['api'])->group(function () {
    Route::post('/ai-analysis', [AIAnalysisController::class, 'analyze']);
    Route::get('/ai-analysis/history', [AIAnalysisController::class, 'getAnalysisHistory']);
    Route::post('/viver-de-renda-ia', [ViverDeRendaIAController::class, 'analyze']);
    Route::post('/diagnostico-seguro', [DiagnosticoSeguroController::class, 'analyze']);
    Route::post('/diagnostico-carteira', [DiagnosticoCarteiraController::class, 'analyze']);
});

// Rotas autenticadas
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('simulations')->name('api.simulations.')->group(function () {
        Route::post('/retirement', [SimulationController::class, 'calculateRetirement'])->name('retirement');
        Route::post('/passive-income', [SimulationController::class, 'calculatePassiveIncome'])->name('passive-income');
        Route::post('/comparison', [SimulationController::class, 'compareInvestments'])->name('comparison');
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
}); 