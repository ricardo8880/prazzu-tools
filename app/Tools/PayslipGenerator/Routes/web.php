<?php

declare(strict_types=1);

use App\Tools\PayslipGenerator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/gerador-holerite', [ToolController::class, 'index'])
    ->name('tools.gerador-holerite.index');
Route::post('/ferramentas/gerador-holerite', [ToolController::class, 'calculate'])
    ->name('tools.gerador-holerite.calculate');

Route::post('/ferramentas/gerador-holerite/exportar/pdf', [ToolController::class, 'exportPdf'])->name('tools.gerador-holerite.export.pdf');
Route::post('/ferramentas/gerador-holerite/exportar/excel', [ToolController::class, 'exportExcel'])->middleware('tool.feature:gerador-holerite,spreadsheet_export')->name('tools.gerador-holerite.export.excel');
