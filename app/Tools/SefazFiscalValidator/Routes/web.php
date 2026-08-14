<?php

declare(strict_types=1);
use App\Tools\SefazFiscalValidator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;
Route::get('/ferramentas/validador-fiscal-sefaz', [ToolController::class, 'index'])->name('tools.validador-fiscal-sefaz.index');
Route::post('/ferramentas/validador-fiscal-sefaz', [ToolController::class, 'calculate'])->name('tools.validador-fiscal-sefaz.calculate');
