<?php

declare(strict_types=1);

use App\Tools\EmployeeCostCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('ferramentas/custo-funcionario-clt')
    ->name('tools.custo-funcionario-clt.')
    ->group(function (): void {
        Route::get('/', [ToolController::class, 'index'])->name('index');
        Route::post('/calcular', [ToolController::class, 'calculate'])
            ->middleware('tool.feature:custo-funcionario-clt,calculate')->name('calculate');
        Route::post('/imprimir', [ToolController::class, 'printCurrent'])
            ->middleware('tool.feature:custo-funcionario-clt,print_report')->name('print');
        Route::post('/baixar/pdf', [ToolController::class, 'downloadPdf'])
            ->middleware('tool.feature:custo-funcionario-clt,print_report')->name('download.pdf');
        Route::post('/baixar/excel', [ToolController::class, 'exportCurrent'])
            ->defaults('format', 'xlsx')->middleware('tool.feature:custo-funcionario-clt,xlsx_export')->name('download.excel');
        Route::post('/exportar/csv', [ToolController::class, 'exportCurrent'])
            ->defaults('format', 'csv')->middleware('tool.feature:custo-funcionario-clt,csv_export')->name('export.csv');
        Route::post('/exportar/xlsx', [ToolController::class, 'exportCurrent'])
            ->defaults('format', 'xlsx')->middleware('tool.feature:custo-funcionario-clt,xlsx_export')->name('export.xlsx');

        Route::post('/lote/calcular', [ToolController::class, 'calculateBatch'])
            ->middleware('tool.feature:custo-funcionario-clt,batch_processing')->name('batch.calculate');
        Route::post('/lote/exportar/csv', [ToolController::class, 'exportBatch'])
            ->defaults('format', 'csv')->middleware([
                'tool.feature:custo-funcionario-clt,batch_processing',
                'tool.feature:custo-funcionario-clt,csv_export',
            ])->name('batch.export.csv');
        Route::post('/lote/exportar/xlsx', [ToolController::class, 'exportBatch'])
            ->defaults('format', 'xlsx')->middleware([
                'tool.feature:custo-funcionario-clt,batch_processing',
                'tool.feature:custo-funcionario-clt,xlsx_export',
            ])->name('batch.export.xlsx');
        Route::post('/lote/imprimir', [ToolController::class, 'printBatch'])
            ->middleware([
                'tool.feature:custo-funcionario-clt,batch_processing',
                'tool.feature:custo-funcionario-clt,professional_report',
            ])->name('batch.print');

        Route::post('/cenarios/comparar', [ToolController::class, 'compareScenarios'])
            ->middleware('tool.feature:custo-funcionario-clt,scenarios')->name('scenarios.compare');
        Route::post('/modalidades/comparar', [ToolController::class, 'compareEmploymentModels'])
            ->middleware('tool.feature:custo-funcionario-clt,employment_model_comparison')->name('models.compare');

        Route::get('/importacao/modelo.csv', [ToolController::class, 'importTemplate'])
            ->defaults('format', 'csv')->middleware('tool.feature:custo-funcionario-clt,csv_import')->name('import.template.csv');
        Route::get('/importacao/modelo.xlsx', [ToolController::class, 'importTemplate'])
            ->defaults('format', 'xlsx')->middleware('tool.feature:custo-funcionario-clt,xlsx_import')->name('import.template.xlsx');
        Route::post('/importacao/preview', [ToolController::class, 'previewImport'])
            ->middleware('tool.import-feature:custo-funcionario-clt,employee-cost,csv_import,xlsx_import')->name('import.preview');
        Route::post('/importacao/processar', [ToolController::class, 'processImport'])
            ->middleware('tool.import-feature:custo-funcionario-clt,employee-cost,csv_import,xlsx_import')->name('import.process');

        Route::middleware('persistence.auth')->name('profiles.')->group(function (): void {
            Route::post('/empresas', [ToolController::class, 'storeCompany'])
                ->middleware('tool.feature:custo-funcionario-clt,company_profiles')->name('companies.store');
            Route::delete('/empresas/{company}', [ToolController::class, 'destroyCompany'])
                ->middleware('tool.feature:custo-funcionario-clt,company_profiles')->name('companies.destroy');
            Route::post('/funcionarios', [ToolController::class, 'storeEmployee'])
                ->middleware('tool.feature:custo-funcionario-clt,employee_profiles')->name('employees.store');
            Route::delete('/funcionarios/{employee}', [ToolController::class, 'destroyEmployee'])
                ->middleware('tool.feature:custo-funcionario-clt,employee_profiles')->name('employees.destroy');
        });

        Route::middleware([
            'tool.feature:custo-funcionario-clt,history',
            'persistence.auth',
        ])->prefix('historico')->name('history.')->group(function (): void {
            Route::get('/', [ToolController::class, 'history'])->name('index');
            Route::get('/{run}', [ToolController::class, 'showHistory'])->name('show');
            Route::get('/{run}/imprimir', [ToolController::class, 'printHistory'])->name('print');
            Route::post('/{run}/repetir', [ToolController::class, 'repeatHistory'])->name('repeat');
            Route::post('/{run}/duplicar', [ToolController::class, 'repeatHistory'])->name('duplicate');
            Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
        });
    });
