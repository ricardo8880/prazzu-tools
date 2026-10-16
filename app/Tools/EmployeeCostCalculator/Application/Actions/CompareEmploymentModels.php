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
        $clt = $this->calculator->execute($data);
        $cltCost = Money::fromMinor((int) $clt->details['amounts']['monthly_cost_minor']);
        $cltGross = Money::fromDecimal($data['salary'])->add(Money::fromDecimal($data['variable_pay']));
        $cltDiscounts = $cltGross->percentage(Percentage::fromString($data['clt_employee_discount_rate']));
        $cltNet = $cltGross->subtract($cltDiscounts)->add(Money::fromDecimal($data['benefits']));
        $pjGross = Money::fromDecimal($data['pj_monthly_invoice']);
        $pjTaxes = $pjGross->percentage(Percentage::fromString($data['pj_tax_rate']));
        $pjExpenses = Money::fromDecimal($data['pj_expenses']);
        $pjNet = $pjGross->subtract($pjTaxes)->subtract($pjExpenses);
        $autonomousGross = Money::fromDecimal($data['autonomous_gross']);
        $autonomousDiscounts = $autonomousGross->percentage(Percentage::fromString($data['autonomous_discount_rate']));
        $autonomousEmployerCharge = $autonomousGross->percentage(Percentage::fromString($data['autonomous_employer_rate']));
        $autonomousNet = $autonomousGross->subtract($autonomousDiscounts);
        $autonomousCost = $autonomousGross->add($autonomousEmployerCharge);

        return [
            'models' => [
                [
                    'model' => 'W-2 employee',
                    'gross' => $cltGross->formatEnUs(),
                    'discounts' => $cltDiscounts->formatEnUs(),
                    'net' => $cltNet->formatEnUs(),
                    'company_cost_minor' => $cltCost->minorAmount(),
                    'company_cost' => $cltCost->formatEnUs(),
                ],
                [
                    'model' => '1099 contractor',
                    'gross' => $pjGross->formatEnUs(),
                    'discounts' => $pjTaxes->add($pjExpenses)->formatEnUs(),
                    'net' => $pjNet->formatEnUs(),
                    'company_cost_minor' => $pjGross->minorAmount(),
                    'company_cost' => $pjGross->formatEnUs(),
                ],
                [
                    'model' => 'Self-employed',
                    'gross' => $autonomousGross->formatEnUs(),
                    'discounts' => $autonomousDiscounts->formatEnUs(),
                    'net' => $autonomousNet->formatEnUs(),
                    'company_cost_minor' => $autonomousCost->minorAmount(),
                    'company_cost' => $autonomousCost->formatEnUs(),
                ],
            ],
            'disclaimer' => 'Financial comparison only. Worker classification depends on the actual relationship and applicable federal/state law.',
        ];
    }
}
