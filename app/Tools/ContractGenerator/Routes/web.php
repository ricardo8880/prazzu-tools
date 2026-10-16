<?php

declare(strict_types=1);

use App\Tools\ContractGenerator\Presentation\Controllers\ContractGeneratorController;
use Illuminate\Support\Facades\Route;

Route::prefix('tools/contract-generator')->name('tools.contract-generator.')->group(function (): void {
    Route::get('/', [ContractGeneratorController::class, 'index'])->name('index');
    Route::post('/draft', [ContractGeneratorController::class, 'build'])->name('build');
    Route::post('/preview', [ContractGeneratorController::class, 'preview'])->name('preview');
    Route::post('/export/pdf', [ContractGeneratorController::class, 'exportPdf'])->name('export.pdf');
    Route::post('/export/xlsx', [ContractGeneratorController::class, 'exportXlsx'])->name('export.xlsx');
    Route::post('/export/word', [ContractGeneratorController::class, 'exportDocx'])->name('export.docx');

    Route::post('/versions', [ContractGeneratorController::class, 'saveVersion'])
        ->middleware(['tool.feature:contract-generator,history', 'persistence.auth'])
        ->name('versions.store');

    Route::middleware(['tool.feature:contract-generator,history', 'persistence.auth'])
        ->prefix('history')->name('history.')->group(function (): void {
            Route::get('/', [ContractGeneratorController::class, 'history'])->name('index');
            Route::delete('/{run}', [ContractGeneratorController::class, 'destroyHistory'])->name('destroy');
            Route::post('/{run}/favorite', [ContractGeneratorController::class, 'toggleFavorite'])
                ->middleware('tool.feature:contract-generator,favorites')->name('favorite');
            Route::get('/compare/versions', [ContractGeneratorController::class, 'compareVersions'])
                ->middleware('tool.feature:contract-generator,version_comparison')->name('compare');
        });
});
