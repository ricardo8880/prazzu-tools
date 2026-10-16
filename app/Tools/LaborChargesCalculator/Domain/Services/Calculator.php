<?php

declare(strict_types=1);

namespace App\Tools\LaborChargesCalculator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Labor\Normative\EmployerChargeRule;
use App\Core\Money\Money;
use App\Core\Tax\US\FederalPayroll2026;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\LaborChargesCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function __construct(private readonly EmployerChargeRule $chargeRule = new EmployerChargeRule) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Incompatible employer-payroll-tax input.');
        }

        $rates = $this->chargeRule->ratesFor($input->futaProfile, $input->sutaRate, $input->otherEmployerCostRate);
        $annualWages = $input->salary->multiply(12);
        $annualBenefits = $input->benefits->multiply(12);
        $socialSecurity = FederalPayroll2026::employerSocialSecurity($annualWages);
        $medicare = FederalPayroll2026::employerMedicare($annualWages);
        $futa = $input->futaProfile === 'futa_exempt' ? Money::zero() : FederalPayroll2026::employerFuta($annualWages);
        $suta = $annualWages->percentage($rates->suta);
        $other = $annualWages->percentage($rates->otherEmployerCosts);
        $annualTaxes = $socialSecurity->add($medicare)->add($futa)->add($suta)->add($other);
        $monthlyTaxes = $annualTaxes->divide(12);
        $monthlyEmployerCost = $input->salary->add($input->benefits)->add($monthlyTaxes);
        $snapshot = $this->chargeRule->snapshot(ReferenceDate::fromString('2026-10-16'));

        return new ToolCalculationResult('employer-payroll-tax-calculator', '2.0.0', [
            new ToolCalculationSummaryItem('monthly_payroll_taxes', 'Estimated monthly employer payroll taxes', $monthlyTaxes->formatEnUs()),
            new ToolCalculationSummaryItem('annual_payroll_taxes', 'Estimated annual employer payroll taxes', $annualTaxes->formatEnUs()),
            new ToolCalculationSummaryItem('social_security', 'Employer Social Security', $socialSecurity->formatEnUs()),
            new ToolCalculationSummaryItem('medicare', 'Employer Medicare', $medicare->formatEnUs()),
            new ToolCalculationSummaryItem('futa', 'FUTA estimate', $futa->formatEnUs()),
            new ToolCalculationSummaryItem('monthly_total', 'Salary + benefits + estimated employer taxes', $monthlyEmployerCost->formatEnUs()),
        ], [
            'input' => $input->toArray(),
            'rates' => $rates->toArray(),
            'amounts' => [
                'annual_wages_minor' => $annualWages->minorAmount(),
                'social_security_minor' => $socialSecurity->minorAmount(),
                'medicare_minor' => $medicare->minorAmount(),
                'futa_minor' => $futa->minorAmount(),
                'suta_minor' => $suta->minorAmount(),
                'other_minor' => $other->minorAmount(),
                'charges_minor' => $monthlyTaxes->minorAmount(),
                'annual_charges_minor' => $annualTaxes->minorAmount(),
                'monthly_total_minor' => $monthlyEmployerCost->minorAmount(),
                'annual_benefits_minor' => $annualBenefits->minorAmount(),
            ],
        ], calculationMemory: new CalculationMemory('2.0.0', [
            new CalculationMemoryStep('fica', 'Employer FICA', '6.2% Social Security up to the 2026 wage base + 1.45% Medicare', ['annual_wages_minor' => $annualWages->minorAmount()], $socialSecurity->add($medicare)->minorAmount()),
            new CalculationMemoryStep('futa', 'FUTA', '0.6% of first $7,000 when maximum credit applies', ['annual_wages_minor' => $annualWages->minorAmount()], $futa->minorAmount()),
            new CalculationMemoryStep('state', 'SUTA and other employer costs', 'annual wages × user-entered effective rates', ['suta_rate' => $rates->suta->toDecimalString(), 'other_rate' => $rates->otherEmployerCosts->toDecimalString()], $suta->add($other)->minorAmount()),
        ], [$snapshot], [
            'State unemployment tax and workers’ compensation rules vary by state.',
            'FUTA credit-reduction states can produce a higher effective FUTA rate.',
            'This is a planning estimate, not a payroll filing calculation.',
        ], true));
    }
}
