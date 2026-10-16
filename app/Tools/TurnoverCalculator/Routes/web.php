<?php

declare(strict_types=1);

use App\Tools\TurnoverCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/employee-turnover-calculator', [ToolController::class, 'index'])
    ->name('tools.employee-turnover-calculator.index');
Route::post('/tools/employee-turnover-calculator', [ToolController::class, 'calculate'])
    ->name('tools.employee-turnover-calculator.calculate');
Route::post('/tools/employee-turnover-calculator/segment-analysis', [ToolController::class, 'analyzeSegments'])
    ->middleware('tool.feature:employee-turnover-calculator,segmented_analysis')
    ->name('tools.employee-turnover-calculator.segmented-analysis');
