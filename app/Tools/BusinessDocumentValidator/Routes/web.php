<?php

declare(strict_types=1);

use App\Tools\BusinessDocumentValidator\Presentation\Controllers\BusinessDocumentValidatorController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/validador-de-cnpj')
    ->name('tools.validador-de-cnpj.')
    ->group(function (): void {
        Route::get('/', [BusinessDocumentValidatorController::class, 'index'])->name('index');
        Route::middleware('tool.feature:validador-de-cnpj,batch_processing')
            ->post('/importar-lote/previsualizar', [BusinessDocumentValidatorController::class, 'previewBatchImport'])->name('batch.preview');
        Route::middleware('tool.feature:validador-de-cnpj,batch_processing')
            ->post('/importar-lote/processar', [BusinessDocumentValidatorController::class, 'processBatchImport'])->name('batch.process');
        Route::middleware('tool.feature:validador-de-cnpj,batch_export')
            ->get('/importar-lote/exportar', [BusinessDocumentValidatorController::class, 'exportBatch'])->name('batch.export');
        Route::middleware('tool.feature:validador-de-cnpj,batch_export')
            ->get('/importar-lote/imprimir', [BusinessDocumentValidatorController::class, 'printBatch'])->name('batch.print');
        Route::middleware('tool.feature:validador-de-cnpj,validate_document')
            ->post('/validar', [BusinessDocumentValidatorController::class, 'validateDocument'])->name('validate');
        Route::middleware('tool.feature:validador-de-cnpj,analyze_consistency')
            ->post('/analisar-inconsistencias', [BusinessDocumentValidatorController::class, 'analyzeConsistency'])->name('analyze-consistency');
        Route::middleware('tool.feature:validador-de-cnpj,lookup_company')
            ->post('/consultar-cnpj', [BusinessDocumentValidatorController::class, 'lookupCompany'])->name('lookup-company');
        Route::middleware('tool.feature:validador-de-cnpj,validate_state_registration')
            ->post('/validar-inscricao-estadual', [BusinessDocumentValidatorController::class, 'validateStateRegistration'])->name('validate-state-registration');

        Route::middleware(['tool.feature:validador-de-cnpj,history', 'persistence.auth'])->prefix('historico')->name('history.')->group(function (): void {
            Route::get('/', [BusinessDocumentValidatorController::class, 'history'])->name('index');
            Route::delete('/{run}', [BusinessDocumentValidatorController::class, 'destroyHistory'])->name('destroy');
        });
    });
