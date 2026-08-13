<?php

declare(strict_types=1);

use App\Tools\TaxInstallmentCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-parcelamento-tributario', [ToolController::class, 'index'])->name('tools.calculadora-parcelamento-tributario.index');
Route::post('/ferramentas/calculadora-parcelamento-tributario', [ToolController::class, 'calculate'])->name('tools.calculadora-parcelamento-tributario.calculate');
Route::get('/ferramentas/calculadora-parcelamento-tributario/export/{format}', [ToolController::class, 'exportCurrent'])
    ->whereIn('format', ['pdf', 'xlsx'])
    ->middleware([
        'tool.feature:calculadora-parcelamento-tributario,report',
        'tool.feature:calculadora-parcelamento-tributario,export',
    ])
    ->name('tools.calculadora-parcelamento-tributario.export');
