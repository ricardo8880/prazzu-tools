<?php

namespace App\Core\Tools\Infrastructure\Services;

use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Infrastructure\Contracts\SensitiveToolPayloadProtector;
use App\Core\Tools\Infrastructure\Enums\SensitiveDataMode;

final class ManifestSensitiveToolPayloadProtector implements SensitiveToolPayloadProtector
{
    public function protect(ToolModule $module, array $payload): array
    {
        $policy = $module->manifest()->sensitiveData;

        if ($policy === null || $policy->mode === SensitiveDataMode::None) {
            return $payload;
        }

        if ($policy->mode === SensitiveDataMode::Encrypted) {
            return $payload;
        }

        foreach ($policy->fields as $field) {
            if (array_key_exists($field, $payload)) {
                $payload[$field] = '[REDACTED]';
            }
        }

        return $payload;
    }
}
