<?php

declare(strict_types=1);

use App\Tools\EmployerInssCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/inss-patronal', [ToolController::class, 'index'])
    ->name('tools.inss-patronal.index');
Route::post('/ferramentas/inss-patronal', [ToolController::class, 'calculate'])
    ->name('tools.inss-patronal.calculate');
Route::post('/ferramentas/inss-patronal/exportar/pdf', [ToolController::class, 'exportPdf'])
    ->name('tools.inss-patronal.export.pdf');
Route::post('/ferramentas/inss-patronal/exportar/excel', [ToolController::class, 'exportExcel'])
    ->middleware('tool.feature:inss-patronal,spreadsheet_export')
    ->name('tools.inss-patronal.export.excel');
