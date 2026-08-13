<?php

declare(strict_types=1);

use App\Tools\WorkingCapitalCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/capital-de-giro', [ToolController::class, 'index'])
    ->name('tools.capital-de-giro.index');
Route::post('/ferramentas/capital-de-giro', [ToolController::class, 'calculate'])
    ->name('tools.capital-de-giro.calculate');
Route::post('/ferramentas/capital-de-giro/projecoes', [ToolController::class, 'project'])
    ->middleware('tool.feature:capital-de-giro,projections')->name('tools.capital-de-giro.projections');
Route::post('/ferramentas/capital-de-giro/exportar/pdf', [ToolController::class, 'exportPdf'])
    ->name('tools.capital-de-giro.export.pdf');
Route::post('/ferramentas/capital-de-giro/exportar/excel', [ToolController::class, 'exportExcel'])
    ->name('tools.capital-de-giro.export.excel');
