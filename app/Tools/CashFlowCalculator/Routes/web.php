<?php

declare(strict_types=1);

use App\Tools\CashFlowCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/fluxo-de-caixa', [ToolController::class, 'index'])
    ->name('tools.fluxo-de-caixa.index');
Route::post('/ferramentas/fluxo-de-caixa', [ToolController::class, 'calculate'])
    ->name('tools.fluxo-de-caixa.calculate');
