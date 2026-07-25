<?php

declare(strict_types=1);

use App\Tools\EmployerInssCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/inss-patronal', [ToolController::class, 'index'])
    ->name('tools.inss-patronal.index');
Route::post('/ferramentas/inss-patronal', [ToolController::class, 'calculate'])
    ->name('tools.inss-patronal.calculate');
