<?php

declare(strict_types=1);

use App\Tools\AdmissionSimulator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/simulador-admissao', [ToolController::class, 'index'])
    ->name('tools.simulador-admissao.index');
Route::post('/ferramentas/simulador-admissao', [ToolController::class, 'calculate'])
    ->name('tools.simulador-admissao.calculate');

Route::post('/ferramentas/simulador-admissao/exportar/pdf', [ToolController::class, 'exportPdf'])->name('tools.simulador-admissao.export.pdf');
Route::post('/ferramentas/simulador-admissao/exportar/excel', [ToolController::class, 'exportExcel'])->middleware('tool.feature:simulador-admissao,spreadsheet_export')->name('tools.simulador-admissao.export.excel');
