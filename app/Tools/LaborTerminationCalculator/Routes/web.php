<?php

declare(strict_types=1);

use App\Tools\LaborTerminationCalculator\Presentation\Controllers\LaborTerminationController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/calculadora-de-rescisao')
    ->name('tools.calculadora-de-rescisao.')
    ->group(function (): void {
        Route::get('/', [LaborTerminationController::class, 'index'])->name('index');
        Route::post('/calcular', [LaborTerminationController::class, 'calculate'])->name('calculate');
        Route::post('/exportar-pdf', [LaborTerminationController::class, 'export'])->name('export');

        Route::middleware('auth')->prefix('historico')->name('history.')->group(function (): void {
            Route::get('/', [LaborTerminationController::class, 'history'])->name('index');
            Route::get('/{run}', [LaborTerminationController::class, 'showHistory'])->name('show');
            Route::get('/{run}/pdf', [LaborTerminationController::class, 'exportHistory'])->name('pdf');
            Route::post('/{run}/repetir', [LaborTerminationController::class, 'repeatHistory'])->name('repeat');
            Route::delete('/{run}', [LaborTerminationController::class, 'destroyHistory'])->name('destroy');
        });
    });
