<?php

declare(strict_types=1);

use App\Tools\BusinessDocumentValidator\Presentation\Controllers\BusinessDocumentValidatorController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/validador-de-cnpj')
    ->name('tools.validador-de-cnpj.')
    ->group(function (): void {
        Route::get('/', [BusinessDocumentValidatorController::class, 'index'])->name('index');
        Route::post('/importar-lote/previsualizar', [BusinessDocumentValidatorController::class, 'previewBatchImport'])->name('batch.preview');
        Route::post('/importar-lote/processar', [BusinessDocumentValidatorController::class, 'processBatchImport'])->name('batch.process');
        Route::get('/importar-lote/exportar', [BusinessDocumentValidatorController::class, 'exportBatch'])->name('batch.export');
        Route::get('/importar-lote/imprimir', [BusinessDocumentValidatorController::class, 'printBatch'])->name('batch.print');
        Route::post('/validar', [BusinessDocumentValidatorController::class, 'validateDocument'])->name('validate');
        Route::post('/analisar-inconsistencias', [BusinessDocumentValidatorController::class, 'analyzeConsistency'])->name('analyze-consistency');
        Route::post('/consultar-cnpj', [BusinessDocumentValidatorController::class, 'lookupCompany'])->name('lookup-company');
        Route::post('/validar-inscricao-estadual', [BusinessDocumentValidatorController::class, 'validateStateRegistration'])->name('validate-state-registration');

        Route::middleware('auth')->prefix('historico')->name('history.')->group(function (): void {
            Route::get('/', [BusinessDocumentValidatorController::class, 'history'])->name('index');
            Route::delete('/{run}', [BusinessDocumentValidatorController::class, 'destroyHistory'])->name('destroy');
        });
    });
