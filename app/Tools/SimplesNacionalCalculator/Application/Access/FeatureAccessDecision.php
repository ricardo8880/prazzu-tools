<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Access;

final readonly class FeatureAccessDecision
{
    private function __construct(
        public bool $allowed,
        public string $reason,
    ) {}

    public static function allow(string $reason = 'feature.allowed'): self
    {
        return new self(true, $reason);
    }

    public static function deny(string $reason = 'feature.plus_required'): self
    {
        return new self(false, $reason);
    }
}
