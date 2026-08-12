<?php

declare(strict_types=1);

use App\Tools\PresumedProfitIrpjCsllCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-irpj-csll-lucro-presumido', [ToolController::class, 'index'])->name('tools.calculadora-irpj-csll-lucro-presumido.index');
Route::post('/ferramentas/calculadora-irpj-csll-lucro-presumido', [ToolController::class, 'calculate'])->name('tools.calculadora-irpj-csll-lucro-presumido.calculate');
Route::get('/ferramentas/calculadora-irpj-csll-lucro-presumido/exportar/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:calculadora-irpj-csll-lucro-presumido,export')->name('tools.calculadora-irpj-csll-lucro-presumido.export');
