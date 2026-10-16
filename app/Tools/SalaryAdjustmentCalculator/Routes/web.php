<?php

declare(strict_types=1);

use App\Tools\SalaryAdjustmentCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/salary-increase-calculator', [ToolController::class, 'index'])
    ->name('tools.salary-increase-calculator.index');
Route::post('/tools/salary-increase-calculator', [ToolController::class, 'calculate'])
    ->name('tools.salary-increase-calculator.calculate');

Route::post('/tools/salary-increase-calculator/export/pdf', [ToolController::class, 'exportPdf'])->name('tools.salary-increase-calculator.export.pdf');
Route::post('/tools/salary-increase-calculator/export/excel', [ToolController::class, 'exportExcel'])->middleware('tool.feature:salary-increase-calculator,spreadsheet_export')->name('tools.salary-increase-calculator.export.excel');
