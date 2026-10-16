<?php

declare(strict_types=1);
use App\Tools\BusinessDocumentValidator\Presentation\Controllers\BusinessDocumentValidatorController;
use Illuminate\Support\Facades\Route;
Route::prefix('tools/us-tax-id-validator')->name('tools.us-tax-id-validator.')->group(function():void{
    Route::get('/',[BusinessDocumentValidatorController::class,'index'])->name('index');
    Route::post('/validate',[BusinessDocumentValidatorController::class,'validateDocument'])->name('validate');
    Route::post('/batch',[BusinessDocumentValidatorController::class,'validateBatch'])->middleware('tool.feature:us-tax-id-validator,batch_validation')->name('batch');
});
