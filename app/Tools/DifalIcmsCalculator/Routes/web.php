<?php

declare(strict_types=1);
use App\Tools\DifalIcmsCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;
Route::get('/ferramentas/calculadora-difal-icms',[ToolController::class,'index'])->name('tools.calculadora-difal-icms.index');
Route::post('/ferramentas/calculadora-difal-icms',[ToolController::class,'calculate'])->name('tools.calculadora-difal-icms.calculate');
Route::post('/ferramentas/calculadora-difal-icms/exportar/{format}',[ToolController::class,'exportCurrent'])->whereIn('format',['pdf','xlsx'])->name('tools.calculadora-difal-icms.export');
