<?php

use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\ToolRegistry;

$registry = app(ToolRegistry::class);

foreach ($registry->modules() as $module) {
    if (! $module instanceof HasWebRoutes) {
        continue;
    }

    $routeFile = $module->webRoutesPath();

    if (! is_file($routeFile)) {
        throw new RuntimeException("Arquivo de rotas da ferramenta não encontrado: {$routeFile}");
    }

    require $routeFile;
}
