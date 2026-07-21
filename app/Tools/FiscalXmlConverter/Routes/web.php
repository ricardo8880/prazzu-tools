<?php

declare(strict_types=1);

use App\Tools\FiscalXmlConverter\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/conversor-fiscal-xml')->name('tools.conversor-fiscal-xml.')->group(function (): void {
    Route::get('/', [ToolController::class, 'index'])->name('index');
    Route::post('/', [ToolController::class, 'convert'])->name('calculate');
    Route::post('/lote', [ToolController::class, 'batch'])->middleware('tool.feature:conversor-fiscal-xml,batch_processing')->name('batch');
    Route::get('/exportar/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['csv','json','xlsx'])->middleware('tool.feature:conversor-fiscal-xml,professional_export')->name('export');
    Route::middleware(['tool.feature:conversor-fiscal-xml,history','persistence.auth'])->prefix('historico')->name('history.')->group(function (): void {
        Route::get('/', [ToolController::class, 'history'])->name('index');
        Route::post('/{run}/reabrir', [ToolController::class, 'repeatHistory'])->name('repeat');
        Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
        Route::get('/{run}/exportar/{format}', [ToolController::class, 'exportHistory'])->whereIn('format', ['csv','json','xlsx'])->middleware('tool.feature:conversor-fiscal-xml,professional_export')->name('export');
    });
});
