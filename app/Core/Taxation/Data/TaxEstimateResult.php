<?php

declare(strict_types=1);

namespace App\Core\Taxation\Data;

use App\Core\Money\Money;
use InvalidArgumentException;

final readonly class TaxEstimateResult
{
    /**
     * @param list<TaxEstimateItem> $items
     * @param list<string> $assumptions
     * @param list<string> $warnings
     */
    public function __construct(
        public string $regime,
        public Money $monthlyTotal,
        public Money $annualTotal,
        public array $items,
        public array $assumptions = [],
        public array $warnings = [],
    ) {
        if (trim($this->regime) === '') {
            throw new InvalidArgumentException('O regime tributário não pode ser vazio.');
        }

        if ($this->monthlyTotal->minorAmount() < 0 || $this->annualTotal->minorAmount() < 0) {
            throw new InvalidArgumentException('Os totais tributários não podem ser negativos.');
        }

        if ($this->monthlyTotal->currency() !== $this->annualTotal->currency()) {
            throw new InvalidArgumentException('Os totais tributários devem usar a mesma moeda.');
        }

        foreach ($this->items as $item) {
            if (! $item instanceof TaxEstimateItem) {
                throw new InvalidArgumentException('A estimativa contém um item tributário inválido.');
            }
        }
    }
}
