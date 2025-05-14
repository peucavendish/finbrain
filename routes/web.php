<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DiagnosticoSeguroController;
use App\Http\Controllers\DiagnosticoCarteiraController;
use App\Http\Controllers\DiagnosticoSucessorioController;
use App\Http\Controllers\DiagnosticoTributarioController;
use App\Http\Controllers\DiagnosticoHoldingController;
use App\Http\Controllers\ViverDeRendaIAController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/diagnostico-seguro-vida', function () {
    return view('life-insurance-diagnostic');
})->name('diagnostico-seguro-vida');

Route::post('/api/diagnostico-seguro', [DiagnosticoSeguroController::class, 'analyze'])
    ->name('api.diagnostico-seguro');

Route::get('/diagnostico-carteira', function () {
    return view('portfolio-diagnostic');
})->name('diagnostico-carteira');

Route::post('/api/diagnostico-carteira', [DiagnosticoCarteiraController::class, 'analyze'])
    ->name('api.diagnostico-carteira');

Route::post('/api/diagnostico-carteira/pdf', [DiagnosticoCarteiraController::class, 'analyzePdf'])
    ->name('api.diagnostico-carteira.pdf');

Route::get('/viver-de-renda-ia', function () {
    return view('viver-de-renda-ia');
})->name('viver-de-renda-ia');

Route::post('/api/viver-de-renda/chat', [ViverDeRendaIAController::class, 'chat'])->name('api.viver-de-renda.chat');

Route::post('/api/viver-de-renda/analyze', [ViverDeRendaIAController::class, 'analyze'])->name('api.viver-de-renda.analyze');

Route::get('/diagnostico-sucessorio', function () {
    return view('succession-diagnostic');
})->name('diagnostico-sucessorio');

Route::post('/api/diagnostico-sucessorio', [DiagnosticoSucessorioController::class, 'analyze'])
    ->name('api.diagnostico-sucessorio');

Route::post('/api/diagnostico-sucessorio/chat', [DiagnosticoSucessorioController::class, 'chat'])
    ->name('api.diagnostico-sucessorio.chat');

Route::get('/diagnostico-tributario', function () {
    return view('tax-diagnostic');
})->name('diagnostico-tributario');

Route::post('/api/diagnostico-tributario', [DiagnosticoTributarioController::class, 'analyze'])
    ->name('api.diagnostico-tributario');

Route::get('/diagnostico-holding', function () {
    return view('holding-diagnostic');
})->name('diagnostico-holding');

Route::post('/api/diagnostico-holding', [DiagnosticoHoldingController::class, 'analyze'])
    ->name('api.diagnostico-holding');

require __DIR__.'/auth.php';
