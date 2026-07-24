<?php

declare(strict_types=1);

use App\Tools\ContractGenerator\Presentation\Controllers\ContractGeneratorController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/gerador-de-contratos')->name('tools.gerador-de-contratos.')->group(function (): void {
    Route::get('/', [ContractGeneratorController::class, 'index'])->name('index');
    Route::post('/rascunho', [ContractGeneratorController::class, 'build'])->name('build');
    Route::post('/visualizar', [ContractGeneratorController::class, 'preview'])->name('preview');
    Route::post('/exportar/pdf', [ContractGeneratorController::class, 'exportPdf'])->name('export.pdf');
    Route::post('/exportar/word', [ContractGeneratorController::class, 'exportDocx'])->name('export.docx');
});
