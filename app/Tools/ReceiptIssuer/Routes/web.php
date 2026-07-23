<?php

declare(strict_types=1);

use App\Tools\ReceiptIssuer\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/emissor-de-recibos')->name('tools.emissor-de-recibos.')->group(function (): void {
    Route::get('/', [ToolController::class, 'index'])->name('index');
    Route::post('/emitir', [ToolController::class, 'issue'])->name('issue');
    Route::post('/exportar-pdf', [ToolController::class, 'exportPdf'])
        ->middleware('tool.feature:emissor-de-recibos,pdf_export')
        ->name('export.pdf');

    Route::get('/lote', [ToolController::class, 'batch'])
        ->middleware('tool.feature:emissor-de-recibos,batch_generation')
        ->name('batch.index');
    Route::post('/lote', [ToolController::class, 'issueBatch'])
        ->middleware(['tool.feature:emissor-de-recibos,batch_generation', 'tool.feature:emissor-de-recibos,pdf_export'])
        ->name('batch.issue');

    Route::middleware(['tool.feature:emissor-de-recibos,history', 'persistence.auth'])
        ->prefix('historico')->name('history.')->group(function (): void {
            Route::get('/', [ToolController::class, 'history'])->name('index');
            Route::post('/{run}/reutilizar', [ToolController::class, 'repeatHistory'])->name('repeat');
            Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
            Route::get('/{run}/exportar-pdf', [ToolController::class, 'exportHistory'])
                ->middleware('tool.feature:emissor-de-recibos,pdf_export')
                ->name('export.pdf');
        });
    Route::middleware(['tool.feature:emissor-de-recibos,saved_profiles', 'persistence.auth'])
        ->prefix('perfis')->name('profiles.')->group(function (): void {
            Route::get('/', [ToolController::class, 'profiles'])->name('index');
            Route::post('/', [ToolController::class, 'storeProfile'])->name('store');
            Route::post('/{profile}/usar', [ToolController::class, 'useProfile'])->whereNumber('profile')->name('use');
            Route::delete('/{profile}', [ToolController::class, 'destroyProfile'])->whereNumber('profile')->name('destroy');
        });

});
