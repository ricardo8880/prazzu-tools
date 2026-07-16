<?php

declare(strict_types=1);

use App\Tools\AccountingFeesCalculator\Presentation\Controllers\AccountingFeesController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/calculadora-de-honorarios-contabeis')
    ->name('tools.calculadora-de-honorarios-contabeis.')
    ->group(function (): void {
        Route::get('/', [AccountingFeesController::class, 'index'])->name('index');
        Route::post('/calcular', [AccountingFeesController::class, 'calculate'])->name('calculate');
        Route::post('/proposta-comercial', [AccountingFeesController::class, 'proposal'])->name('proposal');
        Route::post('/contrato', [AccountingFeesController::class, 'contract'])->name('contract');
        Route::get('/compartilhado/{token}', [AccountingFeesController::class, 'sharedCalculation'])->name('shared');

        Route::middleware('persistence.auth')->prefix('historico')->name('history.')->group(function (): void {
            Route::get('/', [AccountingFeesController::class, 'history'])->name('index');
            Route::get('/exportar', [AccountingFeesController::class, 'exportHistory'])->name('export');
            Route::post('/{calculation}/duplicar', [AccountingFeesController::class, 'duplicateCalculation'])->name('duplicate');
            Route::patch('/{calculation}/favorito', [AccountingFeesController::class, 'toggleFavorite'])->name('favorite');
            Route::post('/{calculation}/compartilhar', [AccountingFeesController::class, 'shareCalculation'])->name('share');
            Route::delete('/{calculation}', [AccountingFeesController::class, 'deleteCalculation'])->name('delete');
        });

        Route::prefix('reajustes')->name('adjustments.')->group(function (): void {
            Route::get('/', [AccountingFeesController::class, 'adjustments'])->name('index');
            Route::post('/', [AccountingFeesController::class, 'calculateAdjustment'])->name('calculate');
            Route::delete('/{adjustment}', [AccountingFeesController::class, 'deleteAdjustment'])->middleware('persistence.auth')->name('delete');
        });

        Route::middleware('persistence.auth')->prefix('crm')->name('crm.')->group(function (): void {
            Route::get('/', [AccountingFeesController::class, 'crm'])->name('index');
            Route::get('/novo', [AccountingFeesController::class, 'createClient'])->name('create');
            Route::post('/', [AccountingFeesController::class, 'storeClient'])->name('store');
            Route::get('/{client}/editar', [AccountingFeesController::class, 'editClient'])->name('edit');
            Route::put('/{client}', [AccountingFeesController::class, 'updateClient'])->name('update');
            Route::delete('/{client}', [AccountingFeesController::class, 'deleteClient'])->name('delete');
        });
    });
