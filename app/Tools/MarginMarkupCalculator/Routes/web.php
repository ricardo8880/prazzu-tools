<?php

declare(strict_types=1);

use App\Tools\MarginMarkupCalculator\Presentation\Controllers\MarginMarkupController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/calculadora-margem-markup')
    ->name('tools.calculadora-margem-markup.')
    ->group(function (): void {
        Route::get('/', [MarginMarkupController::class, 'index'])->name('index');
        Route::get('/compartilhado/{token}', [MarginMarkupController::class, 'shared'])->name('shared.show');
        Route::post('/compartilhado/{token}/desbloquear', [MarginMarkupController::class, 'unlockShared'])->name('shared.unlock');
        Route::post('/calcular', [MarginMarkupController::class, 'calculate'])->name('calculate');
        Route::post('/exportar', [MarginMarkupController::class, 'export'])->name('export');
        Route::post('/exportar-pdf', [MarginMarkupController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/modelo-importacao', [MarginMarkupController::class, 'importTemplate'])->name('import.template');
        Route::post('/importar/pre-visualizar', [MarginMarkupController::class, 'previewImport'])->name('import.preview');
        Route::post('/importar/processar', [MarginMarkupController::class, 'processImport'])->name('import.process');
        Route::post('/calcular-em-lote', [MarginMarkupController::class, 'calculateBatch'])->name('batch.calculate');
        Route::post('/calcular-em-lote/exportar', [MarginMarkupController::class, 'exportBatch'])->name('batch.export');
        Route::post('/simular-cenarios', [MarginMarkupController::class, 'simulateScenarios'])->name('scenarios.simulate');
        Route::post('/simular-cenarios/exportar', [MarginMarkupController::class, 'exportScenarios'])->name('scenarios.export');

        Route::middleware('persistence.auth')->prefix('historico')->name('history.')->group(function (): void {
            Route::get('/', [MarginMarkupController::class, 'history'])->name('index');
            Route::get('/{run}', [MarginMarkupController::class, 'showHistory'])->name('show');
            Route::get('/{run}/pdf', [MarginMarkupController::class, 'exportHistory'])->name('pdf');
            Route::post('/{run}/compartilhar', [MarginMarkupController::class, 'createShare'])->name('share');
            Route::delete('/{run}/compartilhamento', [MarginMarkupController::class, 'revokeShare'])->name('share.revoke');
            Route::post('/{run}/repetir', [MarginMarkupController::class, 'repeatHistory'])->name('repeat');
            Route::delete('/{run}', [MarginMarkupController::class, 'destroyHistory'])->name('destroy');
        });
    });
