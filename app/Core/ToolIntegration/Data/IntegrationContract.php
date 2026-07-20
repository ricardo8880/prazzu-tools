<?php

namespace App\Core\ToolIntegration\Data;

use InvalidArgumentException;

final readonly class IntegrationContract
{
    /** @param array<int, IntegrationField> $fields */
    public function __construct(
        public string $name,
        public int $version,
        public string $description,
        public array $fields,
    ) {
        if (! preg_match('/^[a-z][a-z0-9]*(?:-[a-z0-9]+)*$/', $this->name)) {
            throw new InvalidArgumentException('O contrato deve usar letras minúsculas, números e hífens.');
        }

        if ($this->version < 1) {
            throw new InvalidArgumentException('A versão do contrato deve ser maior que zero.');
        }

        if (trim($this->description) === '') {
            throw new InvalidArgumentException('A descrição do contrato é obrigatória.');
        }

        $names = [];

        foreach ($this->fields as $field) {
            if (! $field instanceof IntegrationField) {
                throw new InvalidArgumentException('Todos os campos devem ser instâncias de IntegrationField.');
            }

            if (isset($names[$field->name])) {
                throw new InvalidArgumentException("O campo [{$field->name}] está duplicado no contrato.");
            }

            $names[$field->name] = true;
        }
    }

    public function key(): string
    {
        return "{$this->name}:v{$this->version}";
    }
}
