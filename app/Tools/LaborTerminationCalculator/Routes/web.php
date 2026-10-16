<?php

declare(strict_types=1);

use App\Tools\LaborTerminationCalculator\Presentation\Controllers\LaborTerminationController;
use Illuminate\Support\Facades\Route;

Route::prefix('tools/final-pay-severance-calculator')
    ->name('tools.final-pay-severance-calculator.')
    ->group(function (): void {
        Route::get('/', [LaborTerminationController::class, 'index'])->name('index');
        Route::middleware('tool.feature:final-pay-severance-calculator,calculate')
            ->post('/calculate', [LaborTerminationController::class, 'calculate'])->name('calculate');
        Route::middleware('tool.feature:final-pay-severance-calculator,current_report')
            ->post('/export-pdf', [LaborTerminationController::class, 'export'])->name('export');
        Route::post('/export-excel', [LaborTerminationController::class, 'exportExcel'])->name('export.excel');

        Route::middleware('persistence.auth')->prefix('history')->name('history.')->group(function (): void {
            Route::middleware('tool.feature:final-pay-severance-calculator,history')
                ->get('/', [LaborTerminationController::class, 'history'])->name('index');
            Route::middleware('tool.feature:final-pay-severance-calculator,history')
                ->get('/{run}', [LaborTerminationController::class, 'showHistory'])->name('show');
            Route::middleware('tool.feature:final-pay-severance-calculator,historical_report')
                ->get('/{run}/pdf', [LaborTerminationController::class, 'exportHistory'])->name('pdf');
            Route::middleware('tool.feature:final-pay-severance-calculator,repeat_history')
                ->post('/{run}/repeat', [LaborTerminationController::class, 'repeatHistory'])->name('repeat');
            Route::middleware('tool.feature:final-pay-severance-calculator,history')
                ->delete('/{run}', [LaborTerminationController::class, 'destroyHistory'])->name('destroy');
        });
    });
