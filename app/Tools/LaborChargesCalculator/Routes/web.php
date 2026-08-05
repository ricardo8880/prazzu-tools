<?php

declare(strict_types=1);

use App\Tools\LaborChargesCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/encargos-trabalhistas', [ToolController::class, 'index'])
    ->name('tools.encargos-trabalhistas.index');
Route::post('/ferramentas/encargos-trabalhistas', [ToolController::class, 'calculate'])
    ->name('tools.encargos-trabalhistas.calculate');
Route::post('/ferramentas/encargos-trabalhistas/exportar/pdf', [ToolController::class, 'exportPdf'])
    ->name('tools.encargos-trabalhistas.export.pdf');
Route::post('/ferramentas/encargos-trabalhistas/exportar/excel', [ToolController::class, 'exportExcel'])
    ->name('tools.encargos-trabalhistas.export.excel');
