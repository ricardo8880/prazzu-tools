<?php

namespace App\Core\Tools\History\Data;

use InvalidArgumentException;

final readonly class RuleVersion
{
    public function __construct(public string $value)
    {
        if (! preg_match('/^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?$/', $this->value)) {
            throw new InvalidArgumentException('A versão da regra deve seguir versionamento semântico.');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
