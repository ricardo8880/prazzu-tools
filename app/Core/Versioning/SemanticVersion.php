<?php

declare(strict_types=1);

namespace App\Core\Versioning;

use InvalidArgumentException;

final readonly class SemanticVersion
{
    public function __construct(public string $value)
    {
        if (! preg_match('/^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?$/', $this->value)) {
            throw new InvalidArgumentException('A versão deve seguir versionamento semântico.');
        }
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
