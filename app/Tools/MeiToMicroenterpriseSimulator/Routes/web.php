<?php

declare(strict_types=1);

use App\Tools\MeiToMicroenterpriseSimulator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/simulador-mei-microempresa', [ToolController::class, 'index'])->name('tools.simulador-mei-microempresa.index');
Route::post('/ferramentas/simulador-mei-microempresa', [ToolController::class, 'calculate'])->name('tools.simulador-mei-microempresa.calculate');
Route::get('/ferramentas/simulador-mei-microempresa/export/{format}', [ToolController::class, 'exportCurrent'])
    ->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:simulador-mei-microempresa,report')->name('tools.simulador-mei-microempresa.export');
