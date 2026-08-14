<?php

declare(strict_types=1);

use App\Tools\EcadRoyaltySimulator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/simulador-ecad-direitos-autorais', [ToolController::class, 'index'])->name('tools.simulador-ecad-direitos-autorais.index');
Route::post('/ferramentas/simulador-ecad-direitos-autorais', [ToolController::class, 'calculate'])->name('tools.simulador-ecad-direitos-autorais.calculate');
