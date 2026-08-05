<?php

declare(strict_types=1);

use App\Tools\EmploymentModelComparator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/comparador-clt-pj-autonomo', [ToolController::class, 'index'])
    ->name('tools.comparador-clt-pj-autonomo.index');
Route::post('/ferramentas/comparador-clt-pj-autonomo', [ToolController::class, 'calculate'])
    ->name('tools.comparador-clt-pj-autonomo.calculate');
Route::post('/ferramentas/comparador-clt-pj-autonomo/exportar/pdf', [ToolController::class, 'exportPdf'])
    ->name('tools.comparador-clt-pj-autonomo.export.pdf');
Route::post('/ferramentas/comparador-clt-pj-autonomo/exportar/excel', [ToolController::class, 'exportExcel'])
    ->name('tools.comparador-clt-pj-autonomo.export.excel');
