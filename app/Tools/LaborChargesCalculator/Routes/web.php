<?php

declare(strict_types=1);

use App\Tools\LaborChargesCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/employer-payroll-tax-calculator', [ToolController::class, 'index'])
    ->name('tools.employer-payroll-tax-calculator.index');
Route::post('/tools/employer-payroll-tax-calculator', [ToolController::class, 'calculate'])
    ->name('tools.employer-payroll-tax-calculator.calculate');
Route::post('/tools/employer-payroll-tax-calculator/export/pdf', [ToolController::class, 'exportPdf'])
    ->name('tools.employer-payroll-tax-calculator.export.pdf');
Route::post('/tools/employer-payroll-tax-calculator/export/excel', [ToolController::class, 'exportExcel'])
    ->middleware('tool.feature:employer-payroll-tax-calculator,spreadsheet_export')
    ->name('tools.employer-payroll-tax-calculator.export.excel');
