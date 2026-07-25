<?php

declare(strict_types=1);

use App\Tools\SalaryAdjustmentCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/reajuste-salarial', [ToolController::class, 'index'])
    ->name('tools.reajuste-salarial.index');
Route::post('/ferramentas/reajuste-salarial', [ToolController::class, 'calculate'])
    ->name('tools.reajuste-salarial.calculate');
