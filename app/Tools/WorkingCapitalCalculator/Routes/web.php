<?php

declare(strict_types=1);

use App\Tools\WorkingCapitalCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/working-capital-calculator', [ToolController::class, 'index'])
    ->name('tools.working-capital-calculator.index');
Route::post('/tools/working-capital-calculator', [ToolController::class, 'calculate'])
    ->name('tools.working-capital-calculator.calculate');
Route::post('/tools/working-capital-calculator/projections', [ToolController::class, 'project'])
    ->middleware('tool.feature:working-capital-calculator,projections')->name('tools.working-capital-calculator.projections');
Route::post('/tools/working-capital-calculator/export/pdf', [ToolController::class, 'exportPdf'])
    ->name('tools.working-capital-calculator.export.pdf');
Route::post('/tools/working-capital-calculator/export/excel', [ToolController::class, 'exportExcel'])
    ->name('tools.working-capital-calculator.export.excel');
