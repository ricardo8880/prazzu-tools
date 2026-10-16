<?php

declare(strict_types=1);
use App\Tools\ProfitDistributionBalanceSimulator\Presentation\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/tools/owner-distribution-planner', [ToolController::class, 'index'])->name('tools.owner-distribution-planner.index');
Route::post('/tools/owner-distribution-planner', [ToolController::class, 'calculate'])->name('tools.owner-distribution-planner.calculate');
Route::get('/tools/owner-distribution-planner/export/{format}', [ToolController::class, 'exportCurrent'])->whereIn('format', ['pdf', 'xlsx'])->middleware('tool.feature:owner-distribution-planner,report')->name('tools.owner-distribution-planner.export');
