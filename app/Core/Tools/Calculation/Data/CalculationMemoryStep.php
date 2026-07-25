<?php

declare(strict_types=1);

namespace App\Core\Tools\Calculation\Data;

use InvalidArgumentException;

final readonly class CalculationMemoryStep
{
    /** @param array<string, int|string|bool|null> $inputs */
    public function __construct(
        public string $key,
        public string $label,
        public string $formula,
        public array $inputs,
        public int|string $result,
        public ?string $roundingPolicy = null,
    ) {
        if (trim($key) === '' || trim($label) === '' || trim($formula) === '') {
            throw new InvalidArgumentException('A etapa da memória precisa de chave, rótulo e fórmula.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'formula' => $this->formula,
            'inputs' => $this->inputs,
            'result' => $this->result,
            'rounding_policy' => $this->roundingPolicy,
        ];
    }
}
