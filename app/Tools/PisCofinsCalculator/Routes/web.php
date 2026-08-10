<?php

declare(strict_types=1);

use App\Tools\PisCofinsCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-pis-cofins',[ToolController::class,'index'])->name('tools.calculadora-pis-cofins.index');
Route::post('/ferramentas/calculadora-pis-cofins',[ToolController::class,'calculate'])->name('tools.calculadora-pis-cofins.calculate');
Route::get('/ferramentas/calculadora-pis-cofins/exportar/{format}',[ToolController::class,'exportCurrent'])->whereIn('format',['pdf','xlsx'])->name('tools.calculadora-pis-cofins.export');
