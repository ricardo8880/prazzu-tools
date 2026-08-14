<?php

declare(strict_types=1);
use App\Tools\CfopAdvisor\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;
Route::get('/ferramentas/consultor-validador-cfop', [ToolController::class, 'index'])->name('tools.consultor-validador-cfop.index');
Route::post('/ferramentas/consultor-validador-cfop', [ToolController::class, 'calculate'])->name('tools.consultor-validador-cfop.calculate');
