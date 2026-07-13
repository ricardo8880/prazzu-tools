<?php

namespace App\Core\Tools\History\Data;

use InvalidArgumentException;

final readonly class ToolHistoryPolicy
{
    /**
     * @param array<int, string> $inputFields
     * @param array<int, string> $resultFields
     * @param array<int, string> $sensitiveFields
     */
    public function __construct(
        public bool $enabled,
        public ?int $retentionDays = null,
        public array $inputFields = [],
        public array $resultFields = [],
        public array $sensitiveFields = [],
    ) {
        if (! $this->enabled) {
            if ($this->retentionDays !== null || $this->inputFields !== [] || $this->resultFields !== [] || $this->sensitiveFields !== []) {
                throw new InvalidArgumentException('Uma política de histórico desabilitada não pode declarar retenção ou campos persistidos.');
            }

            return;
        }

        if ($this->retentionDays === null || $this->retentionDays < 1) {
            throw new InvalidArgumentException('Uma política de histórico ativa deve definir retenção mínima de um dia.');
        }

        $this->assertFieldList($this->inputFields, 'entrada');
        $this->assertFieldList($this->resultFields, 'resultado');
        $this->assertFieldList($this->sensitiveFields, 'sensíveis');

        $allowed = array_unique([...$this->inputFields, ...$this->resultFields]);
        foreach ($this->sensitiveFields as $field) {
            if (! in_array($field, $allowed, true)) {
                throw new InvalidArgumentException("O campo sensível [{$field}] não está autorizado para persistência.");
            }
        }
    }

    public static function disabled(): self
    {
        return new self(false);
    }

    /** @param array<int, string> $fields */
    private function assertFieldList(array $fields, string $label): void
    {
        if (count($fields) !== count(array_unique($fields))) {
            throw new InvalidArgumentException("A lista de campos de {$label} contém duplicidades.");
        }

        foreach ($fields as $field) {
            if (! is_string($field) || ! preg_match('/^[A-Za-z0-9_.-]+$/', $field)) {
                throw new InvalidArgumentException("A lista de campos de {$label} contém um nome inválido.");
            }
        }
    }
}
