<?php

declare(strict_types=1);

use App\Tools\BreakEvenCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/break-even-calculator', [ToolController::class, 'index'])
    ->name('tools.break-even-calculator.index');
Route::post('/tools/break-even-calculator', [ToolController::class, 'calculate'])
    ->name('tools.break-even-calculator.calculate');
Route::post('/tools/break-even-calculator/scenarios', [ToolController::class, 'compareScenarios'])
    ->middleware('tool.feature:break-even-calculator,scenario_comparison')->name('tools.break-even-calculator.scenarios');

Route::post('/tools/break-even-calculator/export/pdf', [ToolController::class, 'exportPdf'])->name('tools.break-even-calculator.export.pdf');
Route::post('/tools/break-even-calculator/export/excel', [ToolController::class, 'exportExcel'])->name('tools.break-even-calculator.export.excel');
