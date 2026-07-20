<?php

declare(strict_types=1);

namespace App\Core\Tools\Calculation\Data;

use InvalidArgumentException;

final readonly class ToolCalculationAction
{
    public function __construct(
        public string $key,
        public string $label,
        public string $type,
        public array $context = [],
    ) {
        if (trim($this->key) === '' || trim($this->label) === '' || trim($this->type) === '') {
            throw new InvalidArgumentException('A ação posterior do cálculo deve possuir chave, rótulo e tipo.');
        }
    }

    /** @return array{key:string,label:string,type:string,context:array<string,mixed>} */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'type' => $this->type,
            'context' => $this->context,
        ];
    }
}
