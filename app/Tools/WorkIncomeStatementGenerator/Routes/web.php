<?php

declare(strict_types=1);

use App\Tools\WorkIncomeStatementGenerator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/declaracao-trabalho-renda', [ToolController::class, 'index'])
    ->name('tools.declaracao-trabalho-renda.index');
Route::post('/ferramentas/declaracao-trabalho-renda', [ToolController::class, 'calculate'])
    ->name('tools.declaracao-trabalho-renda.calculate');
