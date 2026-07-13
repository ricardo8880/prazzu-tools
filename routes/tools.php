<?php

use App\Core\Tools\ToolRegistry;

$registry = app(ToolRegistry::class);

foreach ($registry->modules() as $module) {
    $routeFile = $module->routeFile();

    if ($routeFile !== null && is_file($routeFile)) {
        require $routeFile;
    }
}
