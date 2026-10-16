<?php

declare(strict_types=1);

use App\Tools\MarginMarkupCalculator\Presentation\Controllers\MarginMarkupController;
use Illuminate\Support\Facades\Route;

Route::prefix('tools/margin-markup-calculator')
    ->name('tools.margin-markup-calculator.')
    ->group(function (): void {
        Route::get('/', [MarginMarkupController::class, 'index'])->name('index');
        Route::middleware('tool.feature:margin-markup-calculator,calculate')
            ->post('/calculate', [MarginMarkupController::class, 'calculate'])->name('calculate');
        Route::middleware('tool.feature:margin-markup-calculator,export')
            ->post('/export', [MarginMarkupController::class, 'export'])->name('export');
        Route::middleware('tool.feature:margin-markup-calculator,export')
            ->post('/export-pdf', [MarginMarkupController::class, 'exportPdf'])->name('export.pdf');
        Route::middleware('tool.feature:margin-markup-calculator,batch_processing')
            ->get('/import-template', [MarginMarkupController::class, 'importTemplate'])->name('import.template');
        Route::middleware('tool.feature:margin-markup-calculator,batch_processing')
            ->post('/importar/pre-visualizar', [MarginMarkupController::class, 'previewImport'])->name('import.preview');
        Route::middleware('tool.feature:margin-markup-calculator,batch_processing')
            ->post('/importar/process', [MarginMarkupController::class, 'processImport'])->name('import.process');
        Route::middleware('tool.feature:margin-markup-calculator,batch_processing')
            ->post('/batch-calculate', [MarginMarkupController::class, 'calculateBatch'])->name('batch.calculate');
        Route::middleware(['tool.feature:margin-markup-calculator,batch_processing', 'tool.feature:margin-markup-calculator,export'])
            ->post('/batch-calculate/export', [MarginMarkupController::class, 'exportBatch'])->name('batch.export');
        Route::middleware('tool.feature:margin-markup-calculator,scenarios')
            ->post('/simulate-scenarios', [MarginMarkupController::class, 'simulateScenarios'])->name('scenarios.simulate');
        Route::middleware(['tool.feature:margin-markup-calculator,scenarios', 'tool.feature:margin-markup-calculator,export'])
            ->post('/simulate-scenarios/export', [MarginMarkupController::class, 'exportScenarios'])->name('scenarios.export');

        Route::middleware(['tool.feature:margin-markup-calculator,history', 'persistence.auth'])->prefix('history')->name('history.')->group(function (): void {
            Route::get('/', [MarginMarkupController::class, 'history'])->name('index');
            Route::get('/{run}', [MarginMarkupController::class, 'showHistory'])->name('show');
            Route::get('/{run}/pdf', [MarginMarkupController::class, 'exportHistory'])->name('pdf');
            Route::post('/{run}/repeat', [MarginMarkupController::class, 'repeatHistory'])->name('repeat');
            Route::delete('/{run}', [MarginMarkupController::class, 'destroyHistory'])->name('destroy');
        });
    });
