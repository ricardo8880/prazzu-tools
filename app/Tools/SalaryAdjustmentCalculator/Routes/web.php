<?php

declare(strict_types=1);

use App\Tools\SalaryAdjustmentCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/reajuste-salarial', [ToolController::class, 'index'])
    ->name('tools.reajuste-salarial.index');
Route::post('/ferramentas/reajuste-salarial', [ToolController::class, 'calculate'])
    ->name('tools.reajuste-salarial.calculate');

Route::post('/ferramentas/reajuste-salarial/exportar/pdf', [ToolController::class, 'exportPdf'])->name('tools.reajuste-salarial.export.pdf');
Route::post('/ferramentas/reajuste-salarial/exportar/excel', [ToolController::class, 'exportExcel'])->middleware('tool.feature:reajuste-salarial,spreadsheet_export')->name('tools.reajuste-salarial.export.excel');
