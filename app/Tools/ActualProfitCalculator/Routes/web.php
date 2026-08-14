<?php

declare(strict_types=1);
use App\Tools\ActualProfitCalculator\Presentation\Controllers\ToolController; use Illuminate\Support\Facades\Route;
Route::get('/ferramentas/calculadora-lucro-real',[ToolController::class,'index'])->name('tools.calculadora-lucro-real.index'); Route::post('/ferramentas/calculadora-lucro-real',[ToolController::class,'calculate'])->name('tools.calculadora-lucro-real.calculate');
