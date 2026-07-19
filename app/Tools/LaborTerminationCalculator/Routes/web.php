<?php

declare(strict_types=1);

use App\Tools\LaborTerminationCalculator\Presentation\Controllers\LaborTerminationController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/calculadora-de-rescisao')
    ->name('tools.calculadora-de-rescisao.')
    ->group(function (): void {
        Route::get('/', [LaborTerminationController::class, 'index'])->name('index');
        Route::middleware('tool.feature:calculadora-de-rescisao,calculate')
            ->post('/calcular', [LaborTerminationController::class, 'calculate'])->name('calculate');
        Route::middleware('tool.feature:calculadora-de-rescisao,current_report')
            ->post('/exportar-pdf', [LaborTerminationController::class, 'export'])->name('export');

        Route::middleware('persistence.auth')->prefix('historico')->name('history.')->group(function (): void {
            Route::middleware('tool.feature:calculadora-de-rescisao,history')
                ->get('/', [LaborTerminationController::class, 'history'])->name('index');
            Route::middleware('tool.feature:calculadora-de-rescisao,history')
                ->get('/{run}', [LaborTerminationController::class, 'showHistory'])->name('show');
            Route::middleware('tool.feature:calculadora-de-rescisao,historical_report')
                ->get('/{run}/pdf', [LaborTerminationController::class, 'exportHistory'])->name('pdf');
            Route::middleware('tool.feature:calculadora-de-rescisao,repeat_history')
                ->post('/{run}/repetir', [LaborTerminationController::class, 'repeatHistory'])->name('repeat');
            Route::middleware('tool.feature:calculadora-de-rescisao,history')
                ->delete('/{run}', [LaborTerminationController::class, 'destroyHistory'])->name('destroy');
        });
    });
