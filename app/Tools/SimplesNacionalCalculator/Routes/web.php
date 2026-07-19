<?php

declare(strict_types=1);

use App\Tools\SimplesNacionalCalculator\Presentation\Controllers\SimplesNacionalController;
use App\Tools\SimplesNacionalCalculator\Presentation\Controllers\SimplesNacionalPlusController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/calculadora-simples-nacional')
    ->name('tools.calculadora-simples-nacional.')
    ->group(function (): void {
        Route::get('/', [SimplesNacionalController::class, 'index'])->name('index');
        Route::middleware('tool.feature:calculadora-simples-nacional,calculate')
            ->post('/calcular', [SimplesNacionalController::class, 'calculate'])->name('calculate');
        Route::middleware('tool.feature:calculadora-simples-nacional,alerts')
            ->post('/plus/alertas', [SimplesNacionalPlusController::class, 'alerts'])->name('plus.alerts');
        Route::middleware('tool.feature:calculadora-simples-nacional,compare_scenarios')
            ->post('/plus/comparar-cenarios', [SimplesNacionalPlusController::class, 'compareScenarios'])->name('plus.compare-scenarios');
        Route::middleware('tool.feature:calculadora-simples-nacional,compare_annexes')
            ->post('/plus/comparar-anexos', [SimplesNacionalPlusController::class, 'compareAnnexes'])->name('plus.compare-annexes');
        Route::middleware('tool.feature:calculadora-simples-nacional,annual_projection')
            ->post('/plus/projecao-anual', [SimplesNacionalPlusController::class, 'project'])->name('plus.project');
        Route::middleware(['tool.feature:calculadora-simples-nacional,monthly_history', 'persistence.auth'])->group(function (): void {
            Route::post('/plus/historico', [SimplesNacionalPlusController::class, 'save'])->name('plus.history.store');
            Route::delete('/plus/historico/{calculation}', [SimplesNacionalPlusController::class, 'destroy'])->name('plus.history.destroy');
        });
    });
