<?php
use App\Tools\OvertimeCalculator\Presentation\Controllers\ToolController; use Illuminate\Support\Facades\Route;
Route::get('/ferramentas/calculadora-hora-extra',[ToolController::class,'index'])->name('tools.calculadora-hora-extra.index');
Route::post('/ferramentas/calculadora-hora-extra',[ToolController::class,'calculate'])->name('tools.calculadora-hora-extra.calculate');
Route::post('/ferramentas/calculadora-hora-extra/imprimir',[ToolController::class,'printCurrent'])->name('tools.calculadora-hora-extra.print');
Route::post('/ferramentas/calculadora-hora-extra/exportar',[ToolController::class,'exportCurrent'])->name('tools.calculadora-hora-extra.export');
