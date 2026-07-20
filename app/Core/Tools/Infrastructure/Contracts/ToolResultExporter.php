<?php

namespace App\Core\Tools\Infrastructure\Contracts;

use App\Core\Tools\Contracts\ToolModule;

interface ToolResultExporter
{
    /** @param array<string, mixed> $result */
    public function export(ToolModule $module, array $result, string $format): string;
}
