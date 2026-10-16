<?php

declare(strict_types=1); use App\Tools\TaxReformSimulator\Presentation\Controllers\ToolController; use Illuminate\Support\Facades\Route; Route::get('/tools/sales-tax-calculator',[ToolController::class,'index'])->name('tools.sales-tax-calculator.index'); Route::post('/tools/sales-tax-calculator',[ToolController::class,'calculate'])->name('tools.sales-tax-calculator.calculate');
