<?php

namespace App\Core\Tools\Infrastructure\Services;

use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Infrastructure\Contracts\ToolResultCompatibility;
use App\Core\Versioning\SemanticVersion;
use InvalidArgumentException;

final class ManifestToolResultCompatibility implements ToolResultCompatibility
{
    public function canRead(ToolModule $module, string $toolVersion, int $schemaVersion): bool
    {
        $policy = $module->manifest()->persistence;

        if ($policy === null || ! $policy->enabled) {
            return false;
        }

        if ($schemaVersion < $policy->minimumReadableSchemaVersion || $schemaVersion > $policy->schemaVersion) {
            return false;
        }

        try {
            $stored = new SemanticVersion($toolVersion);
            $current = new SemanticVersion($module->manifest()->version);
        } catch (InvalidArgumentException) {
            return false;
        }

        return $this->major($stored->value) === $this->major($current->value);
    }

    private function major(string $version): string
    {
        return explode('.', $version, 2)[0];
    }
}
