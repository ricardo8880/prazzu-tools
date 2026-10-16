<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Labor\Normative\EmployerChargeRule;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tax\US\FederalPayroll2026;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\EmployeeCostCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function __construct(private readonly EmployerChargeRule $chargeRule = new EmployerChargeRule) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Incompatible employee-cost input.');
        }
        if ($input->monthlyHours < 1 || $input->monthlyHours > 744) {
            throw new InvalidArgumentException('Monthly hours must be between 1 and 744.');
        }

$rates = $this->chargeRule->ratesFor($input->futaProfile, $input->sutaRate, $input->otherEmployerCostRate);
        $monthlyWages = $input->salary->add($input->variablePay);
        $annualWages = $monthlyWages->multiply(12);
        $annualBenefits = $input->benefits->multiply(12);

        $socialSecurity = FederalPayroll2026::employerSocialSecurity($annualWages);
        $medicare = FederalPayroll2026::employerMedicare($annualWages);
        $futa = $input->futaProfile === 'futa_exempt' ? Money::zero() : FederalPayroll2026::employerFuta($annualWages);
        $suta = $annualWages->percentage($rates->suta);
        $otherEmployer = $annualWages->percentage($rates->otherEmployerCosts);
        $annualPayrollTaxes = $socialSecurity->add($medicare)->add($futa)->add($suta)->add($otherEmployer);
        $annual = $annualWages->add($annualBenefits)->add($annualPayrollTaxes);
        $monthly = $annual->divide(12);
        $hourly = $monthly->divide($input->monthlyHours);
        $additional = $annual->subtract($annualWages);
        $additionalPercentage = Percentage::fromBasisPoints(intdiv(($additional->minorAmount() * 10_000) + intdiv($annualWages->minorAmount(), 2), $annualWages->minorAmount()));
        $snapshot = $this->chargeRule->snapshot(ReferenceDate::fromString('2026-10-16'));

        return new ToolCalculationResult(
            toolSlug: 'employee-cost-calculator',
            schemaVersion: '2.0.0',
            summary: [
                new ToolCalculationSummaryItem('monthly_cost', 'Estimated monthly employer cost', $monthly->formatEnUs()),
                new ToolCalculationSummaryItem('annual_cost', 'Estimated annual employer cost', $annual->formatEnUs()),
                new ToolCalculationSummaryItem('hourly_cost', 'Estimated employer cost per hour', $hourly->formatEnUs()),
                new ToolCalculationSummaryItem('payroll_taxes', 'Estimated employer payroll taxes', $annualPayrollTaxes->formatEnUs()),
                new ToolCalculationSummaryItem('additional_percentage', 'Benefits and employer taxes vs. wages', $additionalPercentage->toDecimalString().'%'),
            ],
            details: [
                'input' => $input->toArray(),
                'rates' => $rates->toArray(),
                'amounts' => [
                    'remuneration_minor' => $monthlyWages->minorAmount(),
                    'annual_wages_minor' => $annualWages->minorAmount(),
                    'annual_benefits_minor' => $annualBenefits->minorAmount(),
                    'social_security_minor' => $socialSecurity->minorAmount(),
                    'medicare_minor' => $medicare->minorAmount(),
                    'futa_minor' => $futa->minorAmount(),
                    'suta_minor' => $suta->minorAmount(),
                    'other_employer_minor' => $otherEmployer->minorAmount(),
                    'charges_minor' => $annualPayrollTaxes->divide(12)->minorAmount(),
                    'annual_payroll_taxes_minor' => $annualPayrollTaxes->minorAmount(),
                    'monthly_cost_minor' => $monthly->minorAmount(),
                    'annual_cost_minor' => $annual->minorAmount(),
                    'hourly_cost_minor' => $hourly->minorAmount(),
                ],
            ],
            calculationMemory: new CalculationMemory('2.0.0', [
                new CalculationMemoryStep('annual_wages', 'Annual cash wages', '(monthly salary + variable pay) × 12', ['monthly_wages_minor' => $monthlyWages->minorAmount()], $annualWages->minorAmount()),
                new CalculationMemoryStep('fica', 'Employer FICA', 'Social Security (6.2%, 2026 wage base cap) + Medicare (1.45%, no wage cap)', ['annual_wages_minor' => $annualWages->minorAmount()], $socialSecurity->add($medicare)->minorAmount()),
                new CalculationMemoryStep('futa', 'FUTA estimate', '0.6% of the first $7,000 of annual wages when the maximum state credit applies', ['annual_wages_minor' => $annualWages->minorAmount()], $futa->minorAmount()),
                new CalculationMemoryStep('state_other', 'State and other employer costs', 'annual wages × user-supplied effective SUTA and other employer-cost rates', ['suta_rate' => $rates->suta->toDecimalString(), 'other_rate' => $rates->otherEmployerCosts->toDecimalString()], $suta->add($otherEmployer)->minorAmount()),
                new CalculationMemoryStep('annual_cost', 'Annual employer cost', 'cash wages + benefits + employer payroll taxes', ['wages_minor' => $annualWages->minorAmount(), 'benefits_minor' => $annualBenefits->minorAmount(), 'payroll_taxes_minor' => $annualPayrollTaxes->minorAmount()], $annual->minorAmount()),
            ], [$snapshot], [
                'This calculator uses 2026 federal payroll rates. State and local rules vary and must be supplied by the user.',
                'The 0.6% FUTA estimate assumes the maximum state unemployment credit and no credit-reduction adjustment.',
                'SUTA is modeled as an effective rate on annual wages because state rates and taxable wage bases vary.',
                'Benefits are treated as employer costs and are not automatically treated as taxable wages.',
            ], true),
        );
    }
}
