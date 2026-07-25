<?php

declare(strict_types=1);

use App\Tools\ProfitDistributionCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/distribuicao-de-lucros')->name('tools.distribuicao-de-lucros.')->group(function (): void {
    Route::get('/', [ToolController::class, 'index'])->name('index');
    Route::post('/', [ToolController::class, 'calculate'])->name('calculate');
});
