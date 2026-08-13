<?php

declare(strict_types=1);
use App\Tools\IssCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/calculadora-iss', [ToolController::class, 'index'])->name('tools.calculadora-iss.index');
Route::post('/ferramentas/calculadora-iss', [ToolController::class, 'calculate'])->name('tools.calculadora-iss.calculate');
Route::get('/ferramentas/calculadora-iss/export/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:calculadora-iss,export')->name('tools.calculadora-iss.export');
