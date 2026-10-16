<?php

declare(strict_types=1);

use App\Tools\AdmissionSimulator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/new-hire-cost-calculator', [ToolController::class, 'index'])
    ->name('tools.new-hire-cost-calculator.index');
Route::post('/tools/new-hire-cost-calculator', [ToolController::class, 'calculate'])
    ->name('tools.new-hire-cost-calculator.calculate');

Route::post('/tools/new-hire-cost-calculator/export/pdf', [ToolController::class, 'exportPdf'])->name('tools.new-hire-cost-calculator.export.pdf');
Route::post('/tools/new-hire-cost-calculator/export/excel', [ToolController::class, 'exportExcel'])->middleware('tool.feature:new-hire-cost-calculator,spreadsheet_export')->name('tools.new-hire-cost-calculator.export.excel');
