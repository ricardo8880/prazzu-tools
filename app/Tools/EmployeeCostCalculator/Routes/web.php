<?php

declare(strict_types=1);

use App\Tools\EmployeeCostCalculator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('tools/employee-cost-calculator')
    ->name('tools.employee-cost-calculator.')
    ->group(function (): void {
        Route::get('/', [ToolController::class, 'index'])->name('index');
        Route::post('/calculate', [ToolController::class, 'calculate'])
            ->middleware('tool.feature:employee-cost-calculator,calculate')->name('calculate');
        Route::post('/print', [ToolController::class, 'printCurrent'])
            ->middleware('tool.feature:employee-cost-calculator,print_report')->name('print');
        Route::post('/download/pdf', [ToolController::class, 'downloadPdf'])
            ->middleware('tool.feature:employee-cost-calculator,print_report')->name('download.pdf');
        Route::post('/download/excel', [ToolController::class, 'exportCurrent'])
            ->defaults('format', 'xlsx')->middleware('tool.feature:employee-cost-calculator,xlsx_export')->name('download.excel');
        Route::post('/export/csv', [ToolController::class, 'exportCurrent'])
            ->defaults('format', 'csv')->middleware('tool.feature:employee-cost-calculator,csv_export')->name('export.csv');
        Route::post('/export/xlsx', [ToolController::class, 'exportCurrent'])
            ->defaults('format', 'xlsx')->middleware('tool.feature:employee-cost-calculator,xlsx_export')->name('export.xlsx');

        Route::post('/batch/calculate', [ToolController::class, 'calculateBatch'])
            ->middleware(['tool.feature:employee-cost-calculator,batch_processing', 'tool.feature:employee-cost-calculator,projections'])->name('batch.calculate');
        Route::post('/batch/export/csv', [ToolController::class, 'exportBatch'])
            ->defaults('format', 'csv')->middleware([
                'tool.feature:employee-cost-calculator,batch_processing',
                'tool.feature:employee-cost-calculator,csv_export',
            ])->name('batch.export.csv');
        Route::post('/batch/export/xlsx', [ToolController::class, 'exportBatch'])
            ->defaults('format', 'xlsx')->middleware([
                'tool.feature:employee-cost-calculator,batch_processing',
                'tool.feature:employee-cost-calculator,xlsx_export',
            ])->name('batch.export.xlsx');
        Route::post('/batch/print', [ToolController::class, 'printBatch'])
            ->middleware([
                'tool.feature:employee-cost-calculator,batch_processing',
                'tool.feature:employee-cost-calculator,professional_report',
                'tool.feature:employee-cost-calculator,projections',
            ])->name('batch.print');

        Route::post('/scenarios/compare', [ToolController::class, 'compareScenarios'])
            ->middleware('tool.feature:employee-cost-calculator,scenarios')->name('scenarios.compare');
        Route::post('/models/comparar', [ToolController::class, 'compareEmploymentModels'])
            ->middleware('tool.feature:employee-cost-calculator,employment_model_comparison')->name('models.compare');

        Route::get('/import/template.csv', [ToolController::class, 'importTemplate'])
            ->defaults('format', 'csv')->middleware('tool.feature:employee-cost-calculator,csv_import')->name('import.template.csv');
        Route::get('/import/template.xlsx', [ToolController::class, 'importTemplate'])
            ->defaults('format', 'xlsx')->middleware('tool.feature:employee-cost-calculator,xlsx_import')->name('import.template.xlsx');
        Route::post('/import/preview', [ToolController::class, 'previewImport'])
            ->middleware('tool.import-feature:employee-cost-calculator,employee-cost,csv_import,xlsx_import')->name('import.preview');
        Route::post('/import/process', [ToolController::class, 'processImport'])
            ->middleware('tool.import-feature:employee-cost-calculator,employee-cost,csv_import,xlsx_import')->name('import.process');

        Route::middleware('persistence.auth')->name('profiles.')->group(function (): void {
            Route::post('/companies', [ToolController::class, 'storeCompany'])
                ->middleware('tool.feature:employee-cost-calculator,company_profiles')->name('companies.store');
            Route::delete('/companies/{company}', [ToolController::class, 'destroyCompany'])
                ->middleware('tool.feature:employee-cost-calculator,company_profiles')->name('companies.destroy');
            Route::post('/employees', [ToolController::class, 'storeEmployee'])
                ->middleware('tool.feature:employee-cost-calculator,employee_profiles')->name('employees.store');
            Route::delete('/employees/{employee}', [ToolController::class, 'destroyEmployee'])
                ->middleware('tool.feature:employee-cost-calculator,employee_profiles')->name('employees.destroy');
        });

        Route::middleware([
            'tool.feature:employee-cost-calculator,history',
            'persistence.auth',
        ])->prefix('history')->name('history.')->group(function (): void {
            Route::get('/', [ToolController::class, 'history'])->name('index');
            Route::get('/{run}', [ToolController::class, 'showHistory'])->name('show');
            Route::get('/{run}/print', [ToolController::class, 'printHistory'])->name('print');
            Route::post('/{run}/repeat', [ToolController::class, 'repeatHistory'])->name('repeat');
            Route::post('/{run}/duplicate', [ToolController::class, 'repeatHistory'])->name('duplicate');
            Route::delete('/{run}', [ToolController::class, 'destroyHistory'])->name('destroy');
        });
    });
