<?php

declare(strict_types=1);

use App\Tools\WorkingCapitalCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/capital-de-giro', [ToolController::class, 'index'])
    ->name('tools.capital-de-giro.index');
Route::post('/ferramentas/capital-de-giro', [ToolController::class, 'calculate'])
    ->name('tools.capital-de-giro.calculate');
