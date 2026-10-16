<?php

declare(strict_types=1);

use App\Tools\IncomeStatementGenerator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/earnings-statement-generator', [ToolController::class, 'index'])
    ->name('tools.earnings-statement-generator.index');
Route::post('/tools/earnings-statement-generator', [ToolController::class, 'calculate'])
    ->name('tools.earnings-statement-generator.calculate');
Route::post('/tools/earnings-statement-generator/export/pdf', [ToolController::class, 'downloadPdf'])->name('tools.earnings-statement-generator.export.pdf');
Route::post('/tools/earnings-statement-generator/export/excel', [ToolController::class, 'downloadExcel'])->middleware('tool.feature:earnings-statement-generator,spreadsheet_export')->name('tools.earnings-statement-generator.export.excel');
