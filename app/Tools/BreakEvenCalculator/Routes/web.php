<?php

declare(strict_types=1);

use App\Tools\BreakEvenCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/ponto-de-equilibrio', [ToolController::class, 'index'])
    ->name('tools.ponto-de-equilibrio.index');
Route::post('/ferramentas/ponto-de-equilibrio', [ToolController::class, 'calculate'])
    ->name('tools.ponto-de-equilibrio.calculate');
