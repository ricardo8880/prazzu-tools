<?php

declare(strict_types=1);
use App\Tools\OvertimeCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-hora-extra', [ToolController::class, 'index'])->name('tools.calculadora-hora-extra.index');
Route::post('/ferramentas/calculadora-hora-extra', [ToolController::class, 'calculate'])->name('tools.calculadora-hora-extra.calculate');
Route::post('/ferramentas/calculadora-hora-extra/exportar/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:calculadora-hora-extra,export')->name('tools.calculadora-hora-extra.export');
