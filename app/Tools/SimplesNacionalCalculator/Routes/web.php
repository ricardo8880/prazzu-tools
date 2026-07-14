<?php

declare(strict_types=1);

use App\Tools\SimplesNacionalCalculator\Presentation\Controllers\SimplesNacionalController;
use App\Tools\SimplesNacionalCalculator\Presentation\Controllers\SimplesNacionalPlusController;
use App\Tools\SimplesNacionalCalculator\Presentation\Middleware\EnsureSimplesNacionalFeatureAccess;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/calculadora-simples-nacional')
    ->name('tools.calculadora-simples-nacional.')
    ->group(function (): void {
        Route::get('/', [SimplesNacionalController::class, 'index'])->name('index');
        Route::post('/calcular', [SimplesNacionalController::class, 'calculate'])->name('calculate');
        Route::middleware(EnsureSimplesNacionalFeatureAccess::class.':alerts')
            ->post('/plus/alertas', [SimplesNacionalPlusController::class, 'alerts'])->name('plus.alerts');
        Route::middleware(EnsureSimplesNacionalFeatureAccess::class.':compare_scenarios')
            ->post('/plus/comparar-cenarios', [SimplesNacionalPlusController::class, 'compareScenarios'])->name('plus.compare-scenarios');
        Route::middleware(EnsureSimplesNacionalFeatureAccess::class.':compare_annexes')
            ->post('/plus/comparar-anexos', [SimplesNacionalPlusController::class, 'compareAnnexes'])->name('plus.compare-annexes');
        Route::middleware(EnsureSimplesNacionalFeatureAccess::class.':annual_projection')
            ->post('/plus/projecao-anual', [SimplesNacionalPlusController::class, 'project'])->name('plus.project');
        Route::middleware(EnsureSimplesNacionalFeatureAccess::class.':monthly_history')->group(function (): void {
            Route::post('/plus/historico', [SimplesNacionalPlusController::class, 'save'])->name('plus.history.store');
            Route::delete('/plus/historico/{calculation}', [SimplesNacionalPlusController::class, 'destroy'])->name('plus.history.destroy');
        });
    });
