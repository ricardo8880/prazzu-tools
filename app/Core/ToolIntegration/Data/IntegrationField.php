<?php

namespace App\Core\ToolIntegration\Data;

use InvalidArgumentException;

final readonly class IntegrationField
{
    public function __construct(
        public string $name,
        public string $type,
        public bool $required = false,
    ) {
        if (! preg_match('/^[a-z][a-z0-9_]*$/', $this->name)) {
            throw new InvalidArgumentException('O campo de integração deve usar snake_case.');
        }

        if (! in_array($this->type, ['string', 'integer', 'float', 'boolean', 'array', 'date', 'datetime'], true)) {
            throw new InvalidArgumentException("O tipo de integração [{$this->type}] não é suportado.");
        }
    }
}
