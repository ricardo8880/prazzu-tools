<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Data;

use App\Core\Versioning\SemanticVersion;

final readonly class RuleVersion
{
    public function __construct(public string $value)
    {
        new SemanticVersion($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
