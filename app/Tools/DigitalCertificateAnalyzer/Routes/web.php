<?php

declare(strict_types=1);

use App\Tools\DigitalCertificateAnalyzer\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/ferramentas/analisador-certificado-digital-a1', [ToolController::class, 'index'])->name('tools.analisador-certificado-digital-a1.index');
Route::post('/ferramentas/analisador-certificado-digital-a1', [ToolController::class, 'calculate'])->name('tools.analisador-certificado-digital-a1.calculate');
Route::post('/ferramentas/analisador-certificado-digital-a1/exportar-pdf', [ToolController::class, 'export'])->middleware('tool.feature:analisador-certificado-digital-a1,technical_report')->name('tools.analisador-certificado-digital-a1.export');
