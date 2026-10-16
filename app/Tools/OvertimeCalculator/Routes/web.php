<?php

declare(strict_types=1);
use App\Tools\OvertimeCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/overtime-pay-calculator', [ToolController::class, 'index'])->name('tools.overtime-pay-calculator.index');
Route::post('/tools/overtime-pay-calculator', [ToolController::class, 'calculate'])->name('tools.overtime-pay-calculator.calculate');
Route::post('/tools/overtime-pay-calculator/export/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:overtime-pay-calculator,export')->name('tools.overtime-pay-calculator.export');
