<?php

declare(strict_types=1);

namespace App\Core\Normative;

use App\Core\Versioning\SemanticVersion;

final readonly class NormativeRuleVersion
{
    public function __construct(public string $value)
    {
        new SemanticVersion($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
