<?php

declare(strict_types=1);

use App\Tools\TaxRegimeComparator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/comparador-tributario')
    ->name('tools.comparador-tributario.')
    ->group(function (): void {
        Route::get('/', [ToolController::class, 'index'])->name('index');
        Route::middleware('tool.feature:comparador-tributario,compare_tax_regimes')
            ->post('/', [ToolController::class, 'compare'])->name('compare');
        Route::middleware('tool.feature:comparador-tributario,export')
            ->post('/exportar/{format}', [ToolController::class, 'export'])->whereIn('format', ['csv', 'json'])->name('export');
        Route::middleware('tool.feature:comparador-tributario,professional_report')
            ->post('/relatorio', [ToolController::class, 'report'])->name('report');

        Route::middleware(['tool.feature:comparador-tributario,history', 'persistence.auth'])
            ->prefix('historico')->name('history.')->group(function (): void {
                Route::get('/', [ToolController::class, 'history'])->name('index');
                Route::get('/{run}', [ToolController::class, 'showHistory'])->name('show');
                Route::post('/{run}/repetir', [ToolController::class, 'repeatHistory'])->name('repeat');
                Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
            });
    });
