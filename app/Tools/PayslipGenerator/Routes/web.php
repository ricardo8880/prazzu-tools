<?php

declare(strict_types=1);

use App\Tools\PayslipGenerator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/pay-stub-generator', [ToolController::class, 'index'])
    ->name('tools.pay-stub-generator.index');
Route::post('/tools/pay-stub-generator', [ToolController::class, 'calculate'])
    ->name('tools.pay-stub-generator.calculate');

Route::post('/tools/pay-stub-generator/export/pdf', [ToolController::class, 'exportPdf'])->name('tools.pay-stub-generator.export.pdf');
Route::post('/tools/pay-stub-generator/export/excel', [ToolController::class, 'exportExcel'])->middleware('tool.feature:pay-stub-generator,spreadsheet_export')->name('tools.pay-stub-generator.export.excel');
