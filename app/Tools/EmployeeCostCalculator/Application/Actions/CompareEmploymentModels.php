<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;

final readonly class CompareEmploymentModels
{
    public function __construct(private CalculateTool $calculator) {}

    /** @param array<string, mixed> $data */
    public function execute(array $data): array
    {
        // Plus feature: employment_model_comparison.
        $employee = $this->calculator->execute($data);
        $employeeCost = Money::fromMinor((int) $employee->details['amounts']['monthly_cost_minor']);
        $w2Gross = Money::fromDecimal((string) $data['salary'])->add(Money::fromDecimal((string) $data['variable_pay']));
        $w2Deductions = $w2Gross->percentage(Percentage::fromString((string) $data['w2_employee_deduction_rate']));
        $w2Net = $w2Gross->subtract($w2Deductions)->add(Money::fromDecimal((string) $data['benefits']));

        $contractorGross = Money::fromDecimal((string) $data['contractor_monthly_payment']);
        $contractorTaxes = $contractorGross->percentage(Percentage::fromString((string) $data['contractor_tax_rate']));
        $contractorExpenses = Money::fromDecimal((string) $data['contractor_expenses']);
        $contractorNet = $contractorGross->subtract($contractorTaxes)->subtract($contractorExpenses);

        $selfEmployedGross = Money::fromDecimal((string) $data['self_employed_gross']);
        $selfEmployedTaxes = $selfEmployedGross->percentage(Percentage::fromString((string) $data['self_employed_tax_rate']));
        $payerOverhead = $selfEmployedGross->percentage(Percentage::fromString((string) $data['self_employed_payer_overhead_rate']));
        $selfEmployedNet = $selfEmployedGross->subtract($selfEmployedTaxes);
        $selfEmployedPayerCost = $selfEmployedGross->add($payerOverhead);

        return [
            'models' => [
                ['model' => 'W-2 employee', 'gross' => $w2Gross->formatEnUs(), 'discounts' => $w2Deductions->formatEnUs(), 'net' => $w2Net->formatEnUs(), 'company_cost_minor' => $employeeCost->minorAmount(), 'company_cost' => $employeeCost->formatEnUs()],
                ['model' => '1099 contractor', 'gross' => $contractorGross->formatEnUs(), 'discounts' => $contractorTaxes->add($contractorExpenses)->formatEnUs(), 'net' => $contractorNet->formatEnUs(), 'company_cost_minor' => $contractorGross->minorAmount(), 'company_cost' => $contractorGross->formatEnUs()],
                ['model' => 'Self-employed service provider', 'gross' => $selfEmployedGross->formatEnUs(), 'discounts' => $selfEmployedTaxes->formatEnUs(), 'net' => $selfEmployedNet->formatEnUs(), 'company_cost_minor' => $selfEmployedPayerCost->minorAmount(), 'company_cost' => $selfEmployedPayerCost->formatEnUs()],
            ],
            'disclaimer' => 'Financial comparison only. Worker classification depends on the actual relationship and applicable federal and state law.',
        ];
    }
}
