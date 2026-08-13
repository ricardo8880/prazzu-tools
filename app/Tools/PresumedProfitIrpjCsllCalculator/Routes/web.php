<?php

declare(strict_types=1);

use App\Tools\PresumedProfitIrpjCsllCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-irpj-csll-lucro-presumido', [ToolController::class, 'index'])->name('tools.calculadora-irpj-csll-lucro-presumido.index');
Route::post('/ferramentas/calculadora-irpj-csll-lucro-presumido', [ToolController::class, 'calculate'])->name('tools.calculadora-irpj-csll-lucro-presumido.calculate');
Route::get('/ferramentas/calculadora-irpj-csll-lucro-presumido/exportar/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:calculadora-irpj-csll-lucro-presumido,export')->name('tools.calculadora-irpj-csll-lucro-presumido.export');

Route::middleware(['tool.feature:calculadora-irpj-csll-lucro-presumido,history', 'persistence.auth'])
    ->prefix('/ferramentas/calculadora-irpj-csll-lucro-presumido/historico')->name('tools.calculadora-irpj-csll-lucro-presumido.history.')->group(function (): void {
        Route::get('/', [ToolController::class, 'history'])->name('index');
        Route::post('/{run}/reutilizar', [ToolController::class, 'repeatHistory'])->name('repeat');
        Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
    });
