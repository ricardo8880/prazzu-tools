<?php

declare(strict_types=1);
use App\Tools\NetSalaryCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-salario-liquido', [ToolController::class, 'index'])->name('tools.calculadora-salario-liquido.index');
Route::post('/ferramentas/calculadora-salario-liquido', [ToolController::class, 'calculate'])->name('tools.calculadora-salario-liquido.calculate');
Route::post('/ferramentas/calculadora-salario-liquido/exportar/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:calculadora-salario-liquido,export')->name('tools.calculadora-salario-liquido.export');

Route::middleware(['tool.feature:calculadora-salario-liquido,history', 'persistence.auth'])
    ->prefix('/ferramentas/calculadora-salario-liquido/historico')->name('tools.calculadora-salario-liquido.history.')->group(function (): void {
        Route::get('/', [ToolController::class, 'history'])->name('index');
        Route::post('/{run}/reutilizar', [ToolController::class, 'repeatHistory'])->name('repeat');
        Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
    });
