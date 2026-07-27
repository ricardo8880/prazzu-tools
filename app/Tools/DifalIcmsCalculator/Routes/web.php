<?php
use App\Tools\DifalIcmsCalculator\Presentation\Controllers\ToolController; use Illuminate\Support\Facades\Route;
Route::get('/ferramentas/calculadora-difal-icms',[ToolController::class,'index'])->name('tools.calculadora-difal-icms.index');
Route::post('/ferramentas/calculadora-difal-icms',[ToolController::class,'calculate'])->name('tools.calculadora-difal-icms.calculate');
Route::post('/ferramentas/calculadora-difal-icms/imprimir',[ToolController::class,'printCurrent'])->name('tools.calculadora-difal-icms.print');
Route::post('/ferramentas/calculadora-difal-icms/exportar',[ToolController::class,'exportCurrent'])->name('tools.calculadora-difal-icms.export');
