<?php

declare(strict_types=1);

use App\Tools\AssetDepreciationCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-depreciacao-ativos', [ToolController::class, 'index'])
    ->name('tools.calculadora-depreciacao-ativos.index');
Route::post('/ferramentas/calculadora-depreciacao-ativos', [ToolController::class, 'calculate'])
    ->name('tools.calculadora-depreciacao-ativos.calculate');
Route::get('/ferramentas/calculadora-depreciacao-ativos/export/{format}', [ToolController::class, 'exportCurrent'])
    ->whereIn('format', ['pdf', 'xlsx'])
    ->middleware('tool.feature:calculadora-depreciacao-ativos,export')
    ->name('tools.calculadora-depreciacao-ativos.export');

Route::middleware(['persistence.auth', 'tool.feature:calculadora-depreciacao-ativos,multiple_assets'])
    ->prefix('/ferramentas/calculadora-depreciacao-ativos/cadastro')
    ->name('tools.calculadora-depreciacao-ativos.registry.')
    ->group(function (): void {
        Route::post('/', [ToolController::class, 'storeAsset'])->name('store');
        Route::delete('/{asset}', [ToolController::class, 'destroyAsset'])->name('destroy');
    });
