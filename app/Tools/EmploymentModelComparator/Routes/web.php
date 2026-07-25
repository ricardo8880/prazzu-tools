<?php

declare(strict_types=1);

use App\Tools\EmploymentModelComparator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/comparador-clt-pj-autonomo', [ToolController::class, 'index'])
    ->name('tools.comparador-clt-pj-autonomo.index');
Route::post('/ferramentas/comparador-clt-pj-autonomo', [ToolController::class, 'calculate'])
    ->name('tools.comparador-clt-pj-autonomo.calculate');
