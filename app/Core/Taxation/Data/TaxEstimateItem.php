<?php

declare(strict_types=1);

namespace App\Core\Taxation\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use InvalidArgumentException;

final readonly class TaxEstimateItem
{
    public function __construct(
        public string $code,
        public string $label,
        public Money $monthlyAmount,
        public Money $annualAmount,
        public ?Percentage $effectiveRate = null,
    ) {
        if (trim($this->code) === '' || trim($this->label) === '') {
            throw new InvalidArgumentException('O item tributário deve possuir código e nome.');
        }

        if ($this->monthlyAmount->minorAmount() < 0 || $this->annualAmount->minorAmount() < 0) {
            throw new InvalidArgumentException('O item tributário não pode possuir valores negativos.');
        }

        if ($this->monthlyAmount->currency() !== $this->annualAmount->currency()) {
            throw new InvalidArgumentException('Os valores mensal e anual do item tributário devem usar a mesma moeda.');
        }
    }
}
