<?php

declare(strict_types=1);

use App\Tools\TurnoverCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-turnover', [ToolController::class, 'index'])
    ->name('tools.calculadora-turnover.index');
Route::post('/ferramentas/calculadora-turnover', [ToolController::class, 'calculate'])
    ->name('tools.calculadora-turnover.calculate');
