<?php

declare(strict_types=1);

use App\Tools\AssetDepreciationCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/asset-depreciation-calculator', [ToolController::class, 'index'])
    ->name('tools.asset-depreciation-calculator.index');
Route::post('/tools/asset-depreciation-calculator', [ToolController::class, 'calculate'])
    ->name('tools.asset-depreciation-calculator.calculate');
Route::get('/tools/asset-depreciation-calculator/export/{format}', [ToolController::class, 'exportCurrent'])
    ->whereIn('format', ['pdf', 'xlsx'])
    ->middleware('tool.feature:asset-depreciation-calculator,export')
    ->name('tools.asset-depreciation-calculator.export');

Route::middleware(['persistence.auth', 'tool.feature:asset-depreciation-calculator,multiple_assets'])
    ->prefix('/tools/asset-depreciation-calculator/cadastro')
    ->name('tools.asset-depreciation-calculator.registry.')
    ->group(function (): void {
        Route::post('/', [ToolController::class, 'storeAsset'])->name('store');
        Route::delete('/{asset}', [ToolController::class, 'destroyAsset'])->name('destroy');
    });
