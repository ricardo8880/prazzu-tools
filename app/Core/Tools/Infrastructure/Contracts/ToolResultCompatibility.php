<?php

namespace App\Core\Tools\Infrastructure\Contracts;

use App\Core\Tools\Contracts\ToolModule;

interface ToolResultCompatibility
{
    public function canRead(ToolModule $module, string $toolVersion, int $schemaVersion): bool;
}
