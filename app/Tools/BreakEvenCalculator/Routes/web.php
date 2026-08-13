<?php

declare(strict_types=1);

use App\Tools\BreakEvenCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/ponto-de-equilibrio', [ToolController::class, 'index'])
    ->name('tools.ponto-de-equilibrio.index');
Route::post('/ferramentas/ponto-de-equilibrio', [ToolController::class, 'calculate'])
    ->name('tools.ponto-de-equilibrio.calculate');
Route::post('/ferramentas/ponto-de-equilibrio/cenarios', [ToolController::class, 'compareScenarios'])
    ->middleware('tool.feature:ponto-de-equilibrio,scenario_comparison')->name('tools.ponto-de-equilibrio.scenarios');

Route::post('/ferramentas/ponto-de-equilibrio/exportar/pdf', [ToolController::class, 'exportPdf'])->name('tools.ponto-de-equilibrio.export.pdf');
Route::post('/ferramentas/ponto-de-equilibrio/exportar/excel', [ToolController::class, 'exportExcel'])->name('tools.ponto-de-equilibrio.export.excel');
