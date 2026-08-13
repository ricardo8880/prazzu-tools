<?php

declare(strict_types=1);

use App\Tools\IcmsStCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-icms-st', [ToolController::class, 'index'])->name('tools.calculadora-icms-st.index');
Route::post('/ferramentas/calculadora-icms-st', [ToolController::class, 'calculate'])->name('tools.calculadora-icms-st.calculate');
Route::get('/ferramentas/calculadora-icms-st/export/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:calculadora-icms-st,export')->name('tools.calculadora-icms-st.export');

Route::middleware(['tool.feature:calculadora-icms-st,history', 'persistence.auth'])
    ->prefix('/ferramentas/calculadora-icms-st/historico')->name('tools.calculadora-icms-st.history.')->group(function (): void {
        Route::get('/', [ToolController::class, 'history'])->name('index');
        Route::post('/{run}/reutilizar', [ToolController::class, 'repeatHistory'])->name('repeat');
        Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
    });
