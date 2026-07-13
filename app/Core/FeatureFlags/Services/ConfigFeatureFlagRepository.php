<?php

namespace App\Core\FeatureFlags\Services;

use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;

final class ConfigFeatureFlagRepository implements FeatureFlagRepository
{
    public function enabled(string $flag, bool $default = false): bool
    {
        return (bool) config("features.{$flag}", $default);
    }
}
