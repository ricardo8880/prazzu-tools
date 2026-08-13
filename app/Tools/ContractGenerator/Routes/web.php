<?php

declare(strict_types=1);

use App\Tools\ContractGenerator\Presentation\Controllers\ContractGeneratorController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/gerador-de-contratos')->name('tools.gerador-de-contratos.')->group(function (): void {
    Route::get('/', [ContractGeneratorController::class, 'index'])->name('index');
    Route::post('/rascunho', [ContractGeneratorController::class, 'build'])->name('build');
    Route::post('/visualizar', [ContractGeneratorController::class, 'preview'])->name('preview');
    Route::post('/exportar/pdf', [ContractGeneratorController::class, 'exportPdf'])->name('export.pdf');
    Route::post('/exportar/xlsx', [ContractGeneratorController::class, 'exportXlsx'])->name('export.xlsx');
    Route::post('/exportar/word', [ContractGeneratorController::class, 'exportDocx'])->name('export.docx');

    Route::post('/versoes', [ContractGeneratorController::class, 'saveVersion'])
        ->middleware(['tool.feature:gerador-de-contratos,history', 'persistence.auth'])
        ->name('versions.store');

    Route::middleware(['tool.feature:gerador-de-contratos,history', 'persistence.auth'])
        ->prefix('historico')->name('history.')->group(function (): void {
            Route::get('/', [ContractGeneratorController::class, 'history'])->name('index');
            Route::delete('/{run}', [ContractGeneratorController::class, 'destroyHistory'])->name('destroy');
            Route::post('/{run}/favorito', [ContractGeneratorController::class, 'toggleFavorite'])
                ->middleware('tool.feature:gerador-de-contratos,favorites')->name('favorite');
            Route::get('/comparar/versoes', [ContractGeneratorController::class, 'compareVersions'])
                ->middleware('tool.feature:gerador-de-contratos,version_comparison')->name('compare');
        });
});
