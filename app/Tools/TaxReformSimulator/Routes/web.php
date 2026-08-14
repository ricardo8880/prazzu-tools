<?php

declare(strict_types=1); use App\Tools\TaxReformSimulator\Presentation\Controllers\ToolController; use Illuminate\Support\Facades\Route; Route::get('/ferramentas/simulador-reforma-tributaria-consumo',[ToolController::class,'index'])->name('tools.simulador-reforma-tributaria-consumo.index'); Route::post('/ferramentas/simulador-reforma-tributaria-consumo',[ToolController::class,'calculate'])->name('tools.simulador-reforma-tributaria-consumo.calculate');
