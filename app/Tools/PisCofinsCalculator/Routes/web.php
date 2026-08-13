<?php

declare(strict_types=1);

use App\Tools\PisCofinsCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-pis-cofins', [ToolController::class, 'index'])->name('tools.calculadora-pis-cofins.index');
Route::post('/ferramentas/calculadora-pis-cofins', [ToolController::class, 'calculate'])->name('tools.calculadora-pis-cofins.calculate');
Route::get('/ferramentas/calculadora-pis-cofins/exportar/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:calculadora-pis-cofins,export')->name('tools.calculadora-pis-cofins.export');

Route::middleware(['tool.feature:calculadora-pis-cofins,history', 'persistence.auth'])
    ->prefix('/ferramentas/calculadora-pis-cofins/historico')->name('tools.calculadora-pis-cofins.history.')->group(function (): void {
        Route::get('/', [ToolController::class, 'history'])->name('index');
        Route::post('/{run}/reutilizar', [ToolController::class, 'repeatHistory'])->name('repeat');
        Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
    });
