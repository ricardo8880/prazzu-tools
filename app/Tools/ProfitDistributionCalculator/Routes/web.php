<?php

declare(strict_types=1);

use App\Tools\ProfitDistributionCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('tools/owner-distribution-calculator')->name('tools.owner-distribution-calculator.')->group(function (): void {
    Route::get('/', [ToolController::class, 'index'])->name('index');
    Route::post('/', [ToolController::class, 'calculate'])->name('calculate');
    Route::post('/export/pdf', [ToolController::class, 'downloadPdf'])->name('export.pdf');
    Route::post('/export/excel', [ToolController::class, 'downloadExcel'])->name('export.excel');
});
