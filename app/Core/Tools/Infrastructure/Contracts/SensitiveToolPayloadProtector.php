<?php

namespace App\Core\Tools\Infrastructure\Contracts;

use App\Core\Tools\Contracts\ToolModule;

interface SensitiveToolPayloadProtector
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function protect(ToolModule $module, array $payload): array;
}
