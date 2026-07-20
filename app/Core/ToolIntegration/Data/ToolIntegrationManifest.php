<?php

namespace App\Core\ToolIntegration\Data;

use InvalidArgumentException;

final readonly class ToolIntegrationManifest
{
    /**
     * @param array<int, string> $publishes
     * @param array<int, string> $accepts
     */
    public function __construct(
        public array $publishes = [],
        public array $accepts = [],
    ) {
        foreach ([...$this->publishes, ...$this->accepts] as $contractKey) {
            if (! preg_match('/^[a-z][a-z0-9]*(?:-[a-z0-9]+)*:v[1-9][0-9]*$/', $contractKey)) {
                throw new InvalidArgumentException("O contrato de integração [{$contractKey}] deve usar o formato nome:v1.");
            }
        }

        if (count($this->publishes) !== count(array_unique($this->publishes))) {
            throw new InvalidArgumentException('A lista de contratos publicados contém duplicidades.');
        }

        if (count($this->accepts) !== count(array_unique($this->accepts))) {
            throw new InvalidArgumentException('A lista de contratos aceitos contém duplicidades.');
        }
    }
}
