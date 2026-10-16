<?php

declare(strict_types=1);
use App\Tools\NetSalaryCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/take-home-pay-calculator', [ToolController::class, 'index'])->name('tools.take-home-pay-calculator.index');
Route::post('/tools/take-home-pay-calculator', [ToolController::class, 'calculate'])->name('tools.take-home-pay-calculator.calculate');
Route::post('/tools/take-home-pay-calculator/export/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:take-home-pay-calculator,export')->name('tools.take-home-pay-calculator.export');

Route::middleware(['tool.feature:take-home-pay-calculator,history', 'persistence.auth'])
    ->prefix('/tools/take-home-pay-calculator/history')->name('tools.take-home-pay-calculator.history.')->group(function (): void {
        Route::get('/', [ToolController::class, 'history'])->name('index');
        Route::post('/{run}/reuse', [ToolController::class, 'repeatHistory'])->name('repeat');
        Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
    });
