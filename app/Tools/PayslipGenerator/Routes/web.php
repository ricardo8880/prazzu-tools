<?php

declare(strict_types=1);

use App\Tools\PayslipGenerator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/gerador-holerite', [ToolController::class, 'index'])
    ->name('tools.gerador-holerite.index');
Route::post('/ferramentas/gerador-holerite', [ToolController::class, 'calculate'])
    ->name('tools.gerador-holerite.calculate');
