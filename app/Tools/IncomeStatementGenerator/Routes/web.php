<?php

declare(strict_types=1);

use App\Tools\IncomeStatementGenerator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/declaracao-rendimentos', [ToolController::class, 'index'])
    ->name('tools.declaracao-rendimentos.index');
Route::post('/ferramentas/declaracao-rendimentos', [ToolController::class, 'calculate'])
    ->name('tools.declaracao-rendimentos.calculate');
