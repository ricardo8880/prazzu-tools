<?php

declare(strict_types=1);

use App\Tools\TaxInstallmentCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/irs-payment-plan-calculator', [ToolController::class, 'index'])->name('tools.irs-payment-plan-calculator.index');
Route::post('/tools/irs-payment-plan-calculator', [ToolController::class, 'calculate'])->name('tools.irs-payment-plan-calculator.calculate');
Route::get('/tools/irs-payment-plan-calculator/export/{format}', [ToolController::class, 'exportCurrent'])
    ->whereIn('format', ['pdf', 'xlsx'])
    ->middleware([
        'tool.feature:irs-payment-plan-calculator,report',
        'tool.feature:irs-payment-plan-calculator,export',
    ])
    ->name('tools.irs-payment-plan-calculator.export');
