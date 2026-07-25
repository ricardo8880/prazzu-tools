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
                    'model' => 'CLT',
                    'gross' => $cltGross->formatPtBr(),
                    'discounts' => $cltDiscounts->formatPtBr(),
                    'net' => $cltNet->formatPtBr(),
                    'company_cost_minor' => $cltCost->minorAmount(),
                    'company_cost' => $cltCost->formatPtBr(),
                ],
                [
                    'model' => 'PJ',
                    'gross' => $pjGross->formatPtBr(),
                    'discounts' => $pjTaxes->add($pjExpenses)->formatPtBr(),
                    'net' => $pjNet->formatPtBr(),
                    'company_cost_minor' => $pjGross->minorAmount(),
                    'company_cost' => $pjGross->formatPtBr(),
                ],
                [
                    'model' => 'Autônomo',
                    'gross' => $autonomousGross->formatPtBr(),
                    'discounts' => $autonomousDiscounts->formatPtBr(),
                    'net' => $autonomousNet->formatPtBr(),
                    'company_cost_minor' => $autonomousCost->minorAmount(),
                    'company_cost' => $autonomousCost->formatPtBr(),
                ],
            ],
            'disclaimer' => 'A comparação é exclusivamente numérica. A modalidade correta depende da realidade da relação de trabalho e exige análise jurídica e contábil.',
        ];
    }
}
