<?php

use App\Core\Tools\Contracts\HasApiRoutes;
use App\Core\Tools\ToolRegistry;

$registry = app(ToolRegistry::class);

foreach ($registry->modules() as $module) {
    if (! $module instanceof HasApiRoutes) {
        continue;
    }

    require $module->apiRoutesPath();
}
