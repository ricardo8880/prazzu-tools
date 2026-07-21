<?php

declare(strict_types=1);

use App\Tools\FederalPaymentGuideGenerator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/gerador-darf-gps')->name('tools.gerador-darf-gps.')->group(function (): void {
    Route::get('/', [ToolController::class, 'index'])->name('index');
    Route::post('/', [ToolController::class, 'calculate'])->name('calculate');
    Route::post('/exportar/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['csv', 'json', 'pdf'])->middleware('tool.feature:gerador-darf-gps,professional_export')->name('export');

    Route::middleware(['tool.feature:gerador-darf-gps,history', 'persistence.auth'])->prefix('historico')->name('history.')->group(function (): void {
        Route::get('/', [ToolController::class, 'history'])->name('index');
        Route::post('/{run}/reutilizar', [ToolController::class, 'repeatHistory'])->name('repeat');
        Route::patch('/{run}/favorito', [ToolController::class, 'toggleFavorite'])->name('favorite');
        Route::get('/{run}/exportar/{format}', [ToolController::class, 'exportHistory'])->whereIn('format', ['csv', 'json', 'pdf'])->middleware('tool.feature:gerador-darf-gps,professional_export')->name('export');
        Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
    });
});
