<?php

declare(strict_types=1);

use App\Tools\InvoiceWithholdingCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-retencoes-nota-fiscal',[ToolController::class,'index'])->name('tools.calculadora-retencoes-nota-fiscal.index');
Route::post('/ferramentas/calculadora-retencoes-nota-fiscal',[ToolController::class,'calculate'])->name('tools.calculadora-retencoes-nota-fiscal.calculate');
Route::get('/ferramentas/calculadora-retencoes-nota-fiscal/export/{format}',[ToolController::class,'exportCurrent'])->whereIn('format',['pdf','xlsx'])->middleware('tool.feature:calculadora-retencoes-nota-fiscal,export')->name('tools.calculadora-retencoes-nota-fiscal.export');
