<?php

declare(strict_types=1);

namespace App\Core\Tools\Calculation\Data;

use InvalidArgumentException;

final readonly class ToolCalculationSummaryItem
{
    public function __construct(
        public string $key,
        public string $label,
        public int|float|string $value,
        public ?string $description = null,
    ) {
        if (trim($this->key) === '') {
            throw new InvalidArgumentException('A chave do resumo do cálculo não pode ser vazia.');
        }

        if (trim($this->label) === '') {
            throw new InvalidArgumentException('O rótulo do resumo do cálculo não pode ser vazio.');
        }
    }

    /** @return array{key:string,label:string,value:int|float|string,description:?string} */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'value' => $this->value,
            'description' => $this->description,
        ];
    }
}
