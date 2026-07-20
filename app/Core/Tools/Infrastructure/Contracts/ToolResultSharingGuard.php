<?php

namespace App\Core\Tools\Infrastructure\Contracts;

use App\Core\Tools\Contracts\ToolModule;

interface ToolResultSharingGuard
{
    public function authorize(ToolModule $module, bool $authenticated, bool $containsSensitivePayload): void;

    public function expirationMinutes(ToolModule $module): int;
}
