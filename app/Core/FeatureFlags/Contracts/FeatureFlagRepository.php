<?php

namespace App\Core\FeatureFlags\Contracts;

interface FeatureFlagRepository
{
    public function enabled(string $flag, bool $default = false): bool;
}
