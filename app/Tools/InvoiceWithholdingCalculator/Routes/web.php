<?php

declare(strict_types=1);

use App\Tools\InvoiceWithholdingCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/backup-withholding-calculator', [ToolController::class, 'index'])->name('tools.backup-withholding-calculator.index');
Route::post('/tools/backup-withholding-calculator', [ToolController::class, 'calculate'])->name('tools.backup-withholding-calculator.calculate');
Route::get('/tools/backup-withholding-calculator/export/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:backup-withholding-calculator,export')->name('tools.backup-withholding-calculator.export');

Route::middleware(['tool.feature:backup-withholding-calculator,history', 'persistence.auth'])
    ->prefix('/tools/backup-withholding-calculator/history')->name('tools.backup-withholding-calculator.history.')->group(function (): void {
        Route::get('/', [ToolController::class, 'history'])->name('index');
        Route::post('/{run}/reuse', [ToolController::class, 'repeatHistory'])->name('repeat');
        Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
    });
