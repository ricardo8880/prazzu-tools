<?php

declare(strict_types=1);

use App\Tools\AdmissionSimulator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/simulador-admissao', [ToolController::class, 'index'])
    ->name('tools.simulador-admissao.index');
Route::post('/ferramentas/simulador-admissao', [ToolController::class, 'calculate'])
    ->name('tools.simulador-admissao.calculate');
