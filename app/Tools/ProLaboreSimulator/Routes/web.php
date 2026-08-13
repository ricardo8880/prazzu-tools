<?php

declare(strict_types=1);

use App\Tools\ProLaboreSimulator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/simulador-pro-labore-ideal')->name('tools.simulador-pro-labore-ideal.')->group(function (): void {
    Route::get('/', [ToolController::class, 'index'])->name('index');
    Route::post('/', [ToolController::class, 'calculate'])->name('calculate');
    Route::post('/cenarios', [ToolController::class, 'scenarios'])->middleware('tool.feature:simulador-pro-labore-ideal,scenarios')->name('scenarios');
    Route::post('/exportar/pdf', [ToolController::class, 'exportPdf'])->name('export.pdf');
    Route::post('/exportar/excel', [ToolController::class, 'exportExcel'])->name('export.excel');
});
