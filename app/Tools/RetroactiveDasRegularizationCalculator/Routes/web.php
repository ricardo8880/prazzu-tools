<?php

declare(strict_types=1);
use App\Tools\RetroactiveDasRegularizationCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-das-retroativo-regularizacao-simples', [ToolController::class, 'index'])->name('tools.calculadora-das-retroativo-regularizacao-simples.index');
Route::post('/ferramentas/calculadora-das-retroativo-regularizacao-simples', [ToolController::class, 'calculate'])->name('tools.calculadora-das-retroativo-regularizacao-simples.calculate');
Route::get('/ferramentas/calculadora-das-retroativo-regularizacao-simples/export/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:calculadora-das-retroativo-regularizacao-simples,report')->name('tools.calculadora-das-retroativo-regularizacao-simples.export');
