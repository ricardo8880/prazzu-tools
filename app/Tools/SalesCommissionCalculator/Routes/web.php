<?php

declare(strict_types=1);

use App\Tools\SalesCommissionCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/comissao-vendedores', [ToolController::class, 'index'])
    ->name('tools.comissao-vendedores.index');
Route::post('/ferramentas/comissao-vendedores', [ToolController::class, 'calculate'])
    ->name('tools.comissao-vendedores.calculate');
