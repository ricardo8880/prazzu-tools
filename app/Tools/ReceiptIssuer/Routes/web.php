<?php

declare(strict_types=1);

use App\Tools\ReceiptIssuer\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('tools/receipt-generator')->name('tools.receipt-generator.')->group(function (): void {
    Route::get('/', [ToolController::class, 'index'])->name('index');
    Route::post('/issue', [ToolController::class, 'issue'])->name('issue');
    Route::post('/export/{format}', [ToolController::class, 'exportCurrent'])
        ->whereIn('format', ['pdf', 'xlsx'])
        ->middleware('tool.feature:receipt-generator,pdf_export')
        ->name('export');
    Route::post('/export-pdf', [ToolController::class, 'printCurrent'])
        ->defaults('format', 'pdf')
        ->middleware('tool.feature:receipt-generator,pdf_export')
        ->name('export.pdf');

    Route::get('/batch', [ToolController::class, 'batch'])
        ->middleware('tool.feature:receipt-generator,batch_generation')
        ->name('batch.index');
    Route::post('/batch', [ToolController::class, 'issueBatch'])
        ->middleware(['tool.feature:receipt-generator,batch_generation', 'tool.feature:receipt-generator,pdf_export'])
        ->name('batch.issue');

    Route::middleware(['tool.feature:receipt-generator,history', 'persistence.auth'])
        ->prefix('history')->name('history.')->group(function (): void {
            Route::get('/', [ToolController::class, 'history'])->name('index');
            Route::post('/{run}/reuse', [ToolController::class, 'repeatHistory'])->name('repeat');
            Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
            Route::get('/{run}/export/{format}', [ToolController::class, 'exportHistory'])
                ->whereIn('format', ['pdf', 'xlsx'])
                ->middleware('tool.feature:receipt-generator,pdf_export')
                ->name('export');
            Route::get('/{run}/export-pdf', [ToolController::class, 'exportHistory'])
                ->defaults('format', 'pdf')
                ->middleware('tool.feature:receipt-generator,pdf_export')
                ->name('export.pdf');
        });
    Route::middleware(['tool.feature:receipt-generator,saved_profiles', 'persistence.auth'])
        ->prefix('perfis')->name('profiles.')->group(function (): void {
            Route::get('/', [ToolController::class, 'profiles'])->name('index');
            Route::post('/', [ToolController::class, 'storeProfile'])->name('store');
            Route::post('/{profile}/usar', [ToolController::class, 'useProfile'])->whereNumber('profile')->name('use');
            Route::delete('/{profile}', [ToolController::class, 'destroyProfile'])->whereNumber('profile')->name('destroy');
        });

});
