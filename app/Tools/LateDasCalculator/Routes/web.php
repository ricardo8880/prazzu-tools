<?php

declare(strict_types=1);

use App\Tools\LateDasCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/das-em-atraso', [ToolController::class, 'index'])
    ->name('tools.das-em-atraso.index');
Route::post('/ferramentas/das-em-atraso', [ToolController::class, 'calculate'])
    ->name('tools.das-em-atraso.calculate');
Route::post('/ferramentas/das-em-atraso/exportar/pdf', [ToolController::class, 'exportPdf'])
    ->name('tools.das-em-atraso.export.pdf');
Route::post('/ferramentas/das-em-atraso/exportar/excel', [ToolController::class, 'exportExcel'])
    ->name('tools.das-em-atraso.export.excel');
