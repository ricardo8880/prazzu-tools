<?php

declare(strict_types=1);

use App\Tools\AccountingFeesCalculator\Presentation\Controllers\AccountingFeesController;
use Illuminate\Support\Facades\Route;

Route::prefix('tools/accounting-fee-calculator')
    ->name('tools.accounting-fee-calculator.')
    ->group(function (): void {
        Route::get('/', [AccountingFeesController::class, 'index'])->name('index');
        Route::middleware('tool.feature:accounting-fee-calculator,calculate')
            ->post('/calculate', [AccountingFeesController::class, 'calculate'])->name('calculate');
        Route::post('/export/{format}', [AccountingFeesController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->name('export');
        Route::middleware('tool.feature:accounting-fee-calculator,commercial_proposal')
            ->post('/commercial-proposal', [AccountingFeesController::class, 'proposal'])->name('proposal');
        Route::middleware('tool.feature:accounting-fee-calculator,service_contract')
            ->post('/service-agreement', [AccountingFeesController::class, 'contract'])->name('contract');

        Route::middleware(['tool.feature:accounting-fee-calculator,history', 'persistence.auth'])->prefix('history')->name('history.')->group(function (): void {
            Route::get('/', [AccountingFeesController::class, 'history'])->name('index');
            Route::middleware('tool.feature:accounting-fee-calculator,history_export')
                ->get('/export', [AccountingFeesController::class, 'exportHistory'])->name('export');
            Route::post('/{run}/duplicate', [AccountingFeesController::class, 'duplicateCalculation'])->name('duplicate');
            Route::patch('/{run}/favorite', [AccountingFeesController::class, 'toggleFavorite'])->name('favorite');
            Route::delete('/{run}', [AccountingFeesController::class, 'deleteCalculation'])->name('delete');
        });

        Route::prefix('adjustments')->name('adjustments.')->group(function (): void {
            Route::get('/', [AccountingFeesController::class, 'adjustments'])->name('index');
            Route::middleware('tool.feature:accounting-fee-calculator,adjust_fee')
                ->post('/', [AccountingFeesController::class, 'calculateAdjustment'])->name('calculate');
            Route::delete('/{run}', [AccountingFeesController::class, 'deleteAdjustment'])
                ->middleware(['tool.feature:accounting-fee-calculator,history', 'persistence.auth'])
                ->name('delete');
        });
    });
