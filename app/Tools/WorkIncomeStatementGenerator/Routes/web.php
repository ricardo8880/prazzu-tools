<?php

declare(strict_types=1);

use App\Tools\WorkIncomeStatementGenerator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/employment-income-letter-generator', [ToolController::class, 'index'])
    ->name('tools.employment-income-letter-generator.index');
Route::post('/tools/employment-income-letter-generator', [ToolController::class, 'calculate'])
    ->name('tools.employment-income-letter-generator.calculate');
Route::post('/tools/employment-income-letter-generator/export/pdf', [ToolController::class, 'downloadPdf'])->name('tools.employment-income-letter-generator.export.pdf');
Route::post('/tools/employment-income-letter-generator/export/excel', [ToolController::class, 'downloadExcel'])->middleware('tool.feature:employment-income-letter-generator,spreadsheet_export')->name('tools.employment-income-letter-generator.export.excel');
