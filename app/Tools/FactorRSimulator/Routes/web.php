<?php

declare(strict_types=1);

use App\Tools\FactorRSimulator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/simulador-fator-r', [ToolController::class, 'index'])
    ->name('tools.simulador-fator-r.index');
Route::post('/ferramentas/simulador-fator-r', [ToolController::class, 'calculate'])
    ->name('tools.simulador-fator-r.calculate');
