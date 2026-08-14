<?php

declare(strict_types=1);
use App\Tools\IcmsCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;
Route::get('/ferramentas/calculadora-icms-proprio', [ToolController::class, 'index'])->name('tools.calculadora-icms-proprio.index');
Route::post('/ferramentas/calculadora-icms-proprio', [ToolController::class, 'calculate'])->name('tools.calculadora-icms-proprio.calculate');
