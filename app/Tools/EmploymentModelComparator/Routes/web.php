<?php

declare(strict_types=1);

use App\Tools\EmploymentModelComparator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/employee-vs-contractor-calculator', [ToolController::class, 'index'])
    ->name('tools.employee-vs-contractor-calculator.index');
Route::post('/tools/employee-vs-contractor-calculator', [ToolController::class, 'calculate'])
    ->name('tools.employee-vs-contractor-calculator.calculate');
Route::post('/tools/employee-vs-contractor-calculator/export/pdf', [ToolController::class, 'exportPdf'])
    ->name('tools.employee-vs-contractor-calculator.export.pdf');
Route::post('/tools/employee-vs-contractor-calculator/export/excel', [ToolController::class, 'exportExcel'])
    ->middleware('tool.feature:employee-vs-contractor-calculator,spreadsheet_export')
    ->name('tools.employee-vs-contractor-calculator.export.excel');
