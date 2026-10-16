<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $competence,
        public string $baseSalary,
        public string $taxableAdditionalEarnings = '0',
        public string $nonTaxableEarnings = '0',
        public string $filingStatus = 'single',
        public string $pretaxDeductions = '0',
        public string $stateLocalWithholding = '0',
        public string $additionalFederalWithholding = '0',
        public string $benefitDeductions = '0',
        public string $otherDeductions = '0',
    ) {}

    /** @return array<string, string> */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function grossTaxablePay(): Money
    {
        return Money::fromDecimal($this->baseSalary)->add(Money::fromDecimal($this->taxableAdditionalEarnings));
    }
}
