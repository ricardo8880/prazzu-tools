<?php

use App\Tools\NetSalaryCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-salario-liquido', [ToolController::class, 'index'])
    ->name('tools.calculadora-salario-liquido.index');
Route::post('/ferramentas/calculadora-salario-liquido', [ToolController::class, 'calculate'])
    ->name('tools.calculadora-salario-liquido.calculate');
Route::post('/ferramentas/calculadora-salario-liquido/imprimir', [ToolController::class, 'printCurrent'])
    ->name('tools.calculadora-salario-liquido.print');
Route::post('/ferramentas/calculadora-salario-liquido/exportar', [ToolController::class, 'exportCurrent'])
    ->name('tools.calculadora-salario-liquido.export');
