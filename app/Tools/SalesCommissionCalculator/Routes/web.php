<?php

declare(strict_types=1);

use App\Tools\SalesCommissionCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/sales-commission-calculator', [ToolController::class, 'index'])
    ->name('tools.sales-commission-calculator.index');
Route::post('/tools/sales-commission-calculator', [ToolController::class, 'calculate'])
    ->name('tools.sales-commission-calculator.calculate');
Route::post('/tools/sales-commission-calculator/lote', [ToolController::class, 'calculateBatch'])
    ->middleware('tool.feature:sales-commission-calculator,batch_sellers')->name('tools.sales-commission-calculator.batch');

Route::post('/tools/sales-commission-calculator/export/pdf', [ToolController::class, 'exportPdf'])->name('tools.sales-commission-calculator.export.pdf');
Route::post('/tools/sales-commission-calculator/export/excel', [ToolController::class, 'exportExcel'])->name('tools.sales-commission-calculator.export.excel');
