<?php

declare(strict_types=1);
use App\Tools\ProfitDistributionBalanceSimulator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/simulador-distribuicao-lucros-balanco', [ToolController::class, 'index'])->name('tools.simulador-distribuicao-lucros-balanco.index');
Route::post('/ferramentas/simulador-distribuicao-lucros-balanco', [ToolController::class, 'calculate'])->name('tools.simulador-distribuicao-lucros-balanco.calculate');
Route::get('/ferramentas/simulador-distribuicao-lucros-balanco/export/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:simulador-distribuicao-lucros-balanco,report')->name('tools.simulador-distribuicao-lucros-balanco.export');
