<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Domain\Data;

final readonly class FiscalItem
{
    public function __construct(
        public int $number,
        public string $code,
        public string $description,
        public ?string $ncm,
        public ?string $cfop,
        public string $unit,
        public string $quantity,
        public string $unitValue,
        public string $totalValue,
        public array $taxes,
    ) {}

    public function toArray(): array
    {
        return [
            'number' => $this->number, 'code' => $this->code, 'description' => $this->description,
            'ncm' => $this->ncm, 'cfop' => $this->cfop, 'unit' => $this->unit,
            'quantity' => $this->quantity, 'unit_value' => $this->unitValue,
            'total_value' => $this->totalValue, 'taxes' => $this->taxes,
        ];
    }
}
