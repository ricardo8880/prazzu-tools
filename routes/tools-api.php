<?php

use App\Core\Tools\Api\Http\Controllers\ApiStatusController;
use App\Core\Tools\Api\Http\Controllers\ExecuteToolApiActionController;
use App\Core\Tools\Contracts\HasApiRoutes;
use App\Core\Tools\ToolRegistry;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->middleware(['api.client', 'throttle:tools-api'])
    ->group(function (): void {
        Route::get('/', ApiStatusController::class)->name('status');

        Route::post('/tools/{tool}/{action}', ExecuteToolApiActionController::class)
            ->middleware('api.ability:tools:execute')
            ->where(['tool' => '[a-z0-9]+(?:-[a-z0-9]+)*', 'action' => '[a-z0-9]+(?:-[a-z0-9]+)*'])
            ->name('tools.execute');

        $registry = app(ToolRegistry::class);

        foreach ($registry->modules() as $module) {
            if (! $module instanceof HasApiRoutes) {
                continue;
            }

            require $module->apiRoutesPath();
        }
    });
