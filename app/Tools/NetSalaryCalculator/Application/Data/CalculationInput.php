<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Application\Data;

use App\Core\Dates\Competence;
use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Tools\NetSalaryCalculator\Domain\Data\NetSalaryInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $competence,
        public string $baseSalary,
        public string $taxableAdditionalEarnings = '0',
        public string $nonTaxableEarnings = '0',
        public int $dependents = 0,
        public string $judicialPension = '0',
        public string $transportDiscount = '0',
        public string $mealDiscount = '0',
        public string $healthPlanDiscount = '0',
        public string $otherDiscounts = '0',
    ) {}

    public function toDomain(): NetSalaryInput
    {
        return new NetSalaryInput(
            competence: Competence::fromString($this->competence),
            baseSalary: Money::fromDecimal($this->baseSalary),
            taxableAdditionalEarnings: Money::fromDecimal($this->taxableAdditionalEarnings),
            nonTaxableEarnings: Money::fromDecimal($this->nonTaxableEarnings),
            dependents: $this->dependents,
            judicialPension: Money::fromDecimal($this->judicialPension),
            transportDiscount: Money::fromDecimal($this->transportDiscount),
            mealDiscount: Money::fromDecimal($this->mealDiscount),
            healthPlanDiscount: Money::fromDecimal($this->healthPlanDiscount),
            otherDiscounts: Money::fromDecimal($this->otherDiscounts),
        );
    }

    /** @return array<string, int|string> */
    public function toArray(): array
    {
        return [
            'competence' => $this->competence,
            'base_salary' => $this->baseSalary,
            'taxable_additional_earnings' => $this->taxableAdditionalEarnings,
            'non_taxable_earnings' => $this->nonTaxableEarnings,
            'dependents' => $this->dependents,
            'judicial_pension' => $this->judicialPension,
            'transport_discount' => $this->transportDiscount,
            'meal_discount' => $this->mealDiscount,
            'health_plan_discount' => $this->healthPlanDiscount,
            'other_discounts' => $this->otherDiscounts,
        ];
    }
}
