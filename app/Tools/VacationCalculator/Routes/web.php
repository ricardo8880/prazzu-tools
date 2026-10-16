<?php

declare(strict_types=1);

use App\Tools\VacationCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('tools/pto-calculator')->name('tools.pto-calculator.')->group(function (): void {
    Route::get('/', [ToolController::class, 'index'])->name('index');
    Route::post('/', [ToolController::class, 'calculate'])->name('calculate');
    Route::post('/export/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['csv', 'json', 'pdf', 'xlsx'])->middleware('tool.feature:pto-calculator,professional_export')->name('export');
    Route::middleware(['tool.feature:pto-calculator,history', 'persistence.auth'])->prefix('history')->name('history.')->group(function (): void {
        Route::get('/', [ToolController::class, 'history'])->name('index');
        Route::post('/{run}/repeat', [ToolController::class, 'repeatHistory'])->name('repeat');
        Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
        Route::get('/{run}/export/{format}', [ToolController::class, 'exportHistory'])->whereIn('format', ['csv', 'json', 'pdf', 'xlsx'])->middleware('tool.feature:pto-calculator,professional_export')->name('export');
    });
});
