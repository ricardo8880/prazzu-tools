<?php

declare(strict_types=1);

use App\Tools\MarginMarkupCalculator\Presentation\Controllers\MarginMarkupController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/calculadora-margem-markup')
    ->name('tools.calculadora-margem-markup.')
    ->group(function (): void {
        Route::get('/', [MarginMarkupController::class, 'index'])->name('index');
        Route::post('/calcular', [MarginMarkupController::class, 'calculate'])->name('calculate');
        Route::post('/exportar', [MarginMarkupController::class, 'export'])->name('export');
    });
