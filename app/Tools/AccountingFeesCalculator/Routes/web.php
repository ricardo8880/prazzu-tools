<?php

declare(strict_types=1);

use App\Tools\AccountingFeesCalculator\Presentation\Controllers\AccountingFeesController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/calculadora-de-honorarios-contabeis')
    ->name('tools.calculadora-de-honorarios-contabeis.')
    ->group(function (): void {
        Route::get('/', [AccountingFeesController::class, 'index'])->name('index');
        Route::middleware('tool.feature:calculadora-de-honorarios-contabeis,calculate')
            ->post('/calcular', [AccountingFeesController::class, 'calculate'])->name('calculate');
        Route::middleware('tool.feature:calculadora-de-honorarios-contabeis,commercial_proposal')
            ->post('/proposta-comercial', [AccountingFeesController::class, 'proposal'])->name('proposal');
        Route::middleware('tool.feature:calculadora-de-honorarios-contabeis,service_contract')
            ->post('/contrato', [AccountingFeesController::class, 'contract'])->name('contract');

        Route::middleware(['tool.feature:calculadora-de-honorarios-contabeis,history', 'persistence.auth'])->prefix('historico')->name('history.')->group(function (): void {
            Route::get('/', [AccountingFeesController::class, 'history'])->name('index');
            Route::middleware('tool.feature:calculadora-de-honorarios-contabeis,history_export')
                ->get('/exportar', [AccountingFeesController::class, 'exportHistory'])->name('export');
            Route::post('/{run}/duplicar', [AccountingFeesController::class, 'duplicateCalculation'])->name('duplicate');
            Route::patch('/{run}/favorito', [AccountingFeesController::class, 'toggleFavorite'])->name('favorite');
            Route::delete('/{run}', [AccountingFeesController::class, 'deleteCalculation'])->name('delete');
        });

        Route::prefix('reajustes')->name('adjustments.')->group(function (): void {
            Route::get('/', [AccountingFeesController::class, 'adjustments'])->name('index');
            Route::middleware('tool.feature:calculadora-de-honorarios-contabeis,adjust_fee')
                ->post('/', [AccountingFeesController::class, 'calculateAdjustment'])->name('calculate');
            Route::delete('/{run}', [AccountingFeesController::class, 'deleteAdjustment'])
                ->middleware(['tool.feature:calculadora-de-honorarios-contabeis,history', 'persistence.auth'])
                ->name('delete');
        });
    });
