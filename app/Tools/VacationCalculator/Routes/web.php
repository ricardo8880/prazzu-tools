<?php

declare(strict_types=1);

use App\Tools\VacationCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/calculadora-ferias')->name('tools.calculadora-ferias.')->group(function (): void {
    Route::get('/', [ToolController::class, 'index'])->name('index');
    Route::post('/', [ToolController::class, 'calculate'])->name('calculate');
    Route::post('/exportar/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['csv','json','pdf'])->middleware('tool.feature:calculadora-ferias,professional_export')->name('export');
    Route::middleware('tool.feature:calculadora-ferias,vacation_planning')->group(function (): void {
        Route::get('/planejamento', [ToolController::class, 'planner'])->name('planner');
        Route::post('/planejamento', [ToolController::class, 'plan'])->name('plan');
    });
    Route::middleware(['tool.feature:calculadora-ferias,history','persistence.auth'])->prefix('historico')->name('history.')->group(function (): void {
        Route::get('/', [ToolController::class, 'history'])->name('index');
        Route::post('/{run}/repetir', [ToolController::class, 'repeatHistory'])->name('repeat');
        Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
        Route::get('/{run}/exportar/{format}', [ToolController::class, 'exportHistory'])->whereIn('format',['csv','json','pdf'])->middleware('tool.feature:calculadora-ferias,professional_export')->name('export');
    });
});
