<?php

declare(strict_types=1);

use App\Tools\MarginMarkupCalculator\Presentation\Controllers\MarginMarkupController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/calculadora-margem-markup')
    ->name('tools.calculadora-margem-markup.')
    ->group(function (): void {
        Route::get('/', [MarginMarkupController::class, 'index'])->name('index');
        Route::middleware('tool.feature:calculadora-margem-markup,calculate')
            ->post('/calcular', [MarginMarkupController::class, 'calculate'])->name('calculate');
        Route::middleware('tool.feature:calculadora-margem-markup,export')
            ->post('/exportar', [MarginMarkupController::class, 'export'])->name('export');
        Route::middleware('tool.feature:calculadora-margem-markup,export')
            ->post('/exportar-pdf', [MarginMarkupController::class, 'exportPdf'])->name('export.pdf');
        Route::middleware('tool.feature:calculadora-margem-markup,batch_processing')
            ->get('/modelo-importacao', [MarginMarkupController::class, 'importTemplate'])->name('import.template');
        Route::middleware('tool.feature:calculadora-margem-markup,batch_processing')
            ->post('/importar/pre-visualizar', [MarginMarkupController::class, 'previewImport'])->name('import.preview');
        Route::middleware('tool.feature:calculadora-margem-markup,batch_processing')
            ->post('/importar/processar', [MarginMarkupController::class, 'processImport'])->name('import.process');
        Route::middleware('tool.feature:calculadora-margem-markup,batch_processing')
            ->post('/calcular-em-lote', [MarginMarkupController::class, 'calculateBatch'])->name('batch.calculate');
        Route::middleware(['tool.feature:calculadora-margem-markup,batch_processing', 'tool.feature:calculadora-margem-markup,export'])
            ->post('/calcular-em-lote/exportar', [MarginMarkupController::class, 'exportBatch'])->name('batch.export');
        Route::middleware('tool.feature:calculadora-margem-markup,scenarios')
            ->post('/simular-cenarios', [MarginMarkupController::class, 'simulateScenarios'])->name('scenarios.simulate');
        Route::middleware(['tool.feature:calculadora-margem-markup,scenarios', 'tool.feature:calculadora-margem-markup,export'])
            ->post('/simular-cenarios/exportar', [MarginMarkupController::class, 'exportScenarios'])->name('scenarios.export');

        Route::middleware(['tool.feature:calculadora-margem-markup,history', 'persistence.auth'])->prefix('historico')->name('history.')->group(function (): void {
            Route::get('/', [MarginMarkupController::class, 'history'])->name('index');
            Route::get('/{run}', [MarginMarkupController::class, 'showHistory'])->name('show');
            Route::get('/{run}/pdf', [MarginMarkupController::class, 'exportHistory'])->name('pdf');
            Route::post('/{run}/repetir', [MarginMarkupController::class, 'repeatHistory'])->name('repeat');
            Route::delete('/{run}', [MarginMarkupController::class, 'destroyHistory'])->name('destroy');
        });
    });
