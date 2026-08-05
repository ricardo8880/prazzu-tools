<?php

declare(strict_types=1);
use App\Tools\NetSalaryCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;
Route::get('/ferramentas/calculadora-salario-liquido',[ToolController::class,'index'])->name('tools.calculadora-salario-liquido.index');
Route::post('/ferramentas/calculadora-salario-liquido',[ToolController::class,'calculate'])->name('tools.calculadora-salario-liquido.calculate');
Route::post('/ferramentas/calculadora-salario-liquido/exportar/{format}',[ToolController::class,'exportCurrent'])->whereIn('format',['pdf','xlsx'])->name('tools.calculadora-salario-liquido.export');
