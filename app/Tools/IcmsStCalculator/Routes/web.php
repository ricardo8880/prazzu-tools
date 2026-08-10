<?php

declare(strict_types=1);

use App\Tools\IcmsStCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-icms-st',[ToolController::class,'index'])->name('tools.calculadora-icms-st.index');
Route::post('/ferramentas/calculadora-icms-st',[ToolController::class,'calculate'])->name('tools.calculadora-icms-st.calculate');
Route::get('/ferramentas/calculadora-icms-st/export/{format}',[ToolController::class,'exportCurrent'])->whereIn('format',['pdf','xlsx'])->name('tools.calculadora-icms-st.export');
