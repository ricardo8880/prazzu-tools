<?php

declare(strict_types=1);

use App\Tools\CashFlowCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/cash-flow-calculator', [ToolController::class, 'index'])
    ->name('tools.cash-flow-calculator.index');
Route::post('/tools/cash-flow-calculator', [ToolController::class, 'calculate'])
    ->name('tools.cash-flow-calculator.calculate');
Route::post('/tools/cash-flow-calculator/scenarios', [ToolController::class, 'compareScenarios'])
    ->middleware('tool.feature:cash-flow-calculator,cash_flow_scenarios')->name('tools.cash-flow-calculator.scenarios');
Route::post('/tools/cash-flow-calculator/export/pdf', [ToolController::class, 'exportPdf'])
    ->name('tools.cash-flow-calculator.export.pdf');
Route::post('/tools/cash-flow-calculator/export/excel', [ToolController::class, 'exportExcel'])
    ->name('tools.cash-flow-calculator.export.excel');
