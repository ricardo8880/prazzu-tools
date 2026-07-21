<?php

declare(strict_types=1);

use App\Tools\ProLaboreProfitDistributionCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/calculadora-pro-labore-distribuicao-lucros')
    ->name('tools.calculadora-pro-labore-distribuicao-lucros.')
    ->group(function (): void {
        Route::get('/', [ToolController::class, 'index'])->name('index');
        Route::post('/', [ToolController::class, 'calculate'])->name('calculate');
        Route::post('/simular-cenarios', [ToolController::class, 'simulate'])->name('simulate');
        Route::post('/exportar/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['csv', 'json', 'pdf'])->name('export');

        Route::middleware('persistence.auth')->prefix('historico')->name('history.')->group(function (): void {
            Route::get('/', [ToolController::class, 'history'])->name('index');
            Route::get('/{run}', [ToolController::class, 'showHistory'])->name('show');
            Route::get('/{run}/exportar/{format}', [ToolController::class, 'exportHistory'])->whereIn('format', ['csv', 'json', 'pdf'])->name('export');
            Route::post('/{run}/repetir', [ToolController::class, 'repeatHistory'])->name('repeat');
            Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
        });
    });
