<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Tax\US\FederalIncomeTax2026;
use App\Core\Tax\US\FederalPayroll2026;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\NetSalaryCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Incompatible take-home-pay input.');
        }

        $taxableGross = $input->grossTaxablePay();
        $nonTaxable = Money::fromDecimal($input->nonTaxableEarnings);
        $pretax = Money::fromDecimal($input->pretaxDeductions);
        if ($pretax->minorAmount() > $taxableGross->minorAmount()) {
            throw new InvalidArgumentException('Pre-tax deductions cannot exceed taxable gross pay.');
        }

        $monthlyFederalTaxable = $taxableGross->subtract($pretax);
        $annualFederalTaxable = $monthlyFederalTaxable->multiply(12);
        $federalIncomeTax = FederalIncomeTax2026::estimatedAnnualTax($annualFederalTaxable, $input->filingStatus)->divide(12);

        // FICA uses gross covered wages; pretax deductions are not universally exempt from FICA.
        // For a conservative general estimator, the entered pretax amount reduces federal income-tax
        // income only and does not automatically reduce Social Security/Medicare wages.
        $annualWages = $taxableGross->multiply(12);
        $socialSecurity = FederalPayroll2026::employeeSocialSecurity($annualWages)->divide(12);
        $medicare = FederalPayroll2026::employeeMedicare($annualWages)->divide(12);
        $additionalMedicare = FederalPayroll2026::additionalMedicareWithholding($annualWages)->divide(12);

        $additionalFederal = Money::fromDecimal($input->additionalFederalWithholding);
        $stateLocal = Money::fromDecimal($input->stateLocalWithholding);
        $benefits = Money::fromDecimal($input->benefitDeductions);
        $other = Money::fromDecimal($input->otherDeductions);
        $cashGross = $taxableGross->add($nonTaxable);
        $totalDeductions = $pretax
            ->add($federalIncomeTax)
            ->add($additionalFederal)
            ->add($socialSecurity)
            ->add($medicare)
            ->add($additionalMedicare)
            ->add($stateLocal)
            ->add($benefits)
            ->add($other);
        if ($totalDeductions->minorAmount() > $cashGross->minorAmount()) {
            throw new InvalidArgumentException('Total deductions cannot exceed gross cash pay.');
        }
        $net = $cashGross->subtract($totalDeductions);

        $memory = new CalculationMemory('2.0.0', [
            new CalculationMemoryStep('gross', 'Gross cash pay', 'base salary + taxable additional earnings + non-taxable earnings', [], $cashGross->minorAmount()),
            new CalculationMemoryStep('federal_income_tax', 'Estimated federal income tax', 'annualized taxable pay − 2026 standard deduction, then 2026 marginal brackets ÷ 12', ['filing_status' => $input->filingStatus, 'annual_taxable_minor' => $annualFederalTaxable->minorAmount()], $federalIncomeTax->minorAmount()),
            new CalculationMemoryStep('fica', 'Employee FICA', 'Social Security 6.2% up to the 2026 wage base + Medicare 1.45% + applicable Additional Medicare withholding', ['annual_wages_minor' => $annualWages->minorAmount()], $socialSecurity->add($medicare)->add($additionalMedicare)->minorAmount()),
            new CalculationMemoryStep('net', 'Estimated take-home pay', 'gross cash pay − all entered and estimated deductions', ['deductions_minor' => $totalDeductions->minorAmount()], $net->minorAmount()),
        ], assumptions: [
            'Federal income tax is a simplified annual-liability estimate, not the IRS Form W-4 payroll withholding worksheet.',
            'State and local withholding is entered by the user because rules vary by jurisdiction.',
            'Pre-tax deductions are assumed to reduce federal taxable income but are not automatically excluded from FICA wages.',
        ], isEstimate: true);

        return new ToolCalculationResult('take-home-pay-calculator', '2.0.0', [
            new ToolCalculationSummaryItem('gross', 'Gross cash pay', $cashGross->formatEnUs()),
            new ToolCalculationSummaryItem('federal_tax', 'Estimated federal income tax', $federalIncomeTax->formatEnUs()),
            new ToolCalculationSummaryItem('fica', 'Social Security + Medicare', $socialSecurity->add($medicare)->add($additionalMedicare)->formatEnUs()),
            new ToolCalculationSummaryItem('deductions', 'Total deductions', $totalDeductions->formatEnUs()),
            new ToolCalculationSummaryItem('net', 'Estimated take-home pay', $net->formatEnUs()),
        ], [
            'input' => $input->toArray(),
            'gross_minor' => $cashGross->minorAmount(),
            'taxable_gross_minor' => $taxableGross->minorAmount(),
            'pretax_minor' => $pretax->minorAmount(),
            'federal_income_tax_minor' => $federalIncomeTax->minorAmount(),
            'social_security_minor' => $socialSecurity->minorAmount(),
            'medicare_minor' => $medicare->minorAmount(),
            'additional_medicare_minor' => $additionalMedicare->minorAmount(),
            'state_local_minor' => $stateLocal->minorAmount(),
            'user_discounts_minor' => $benefits->add($other)->add($additionalFederal)->minorAmount(),
            'deductions_minor' => $totalDeductions->minorAmount(),
            'net_minor' => $net->minorAmount(),
        ], [
            new ToolCalculationWarning('estimate', 'Actual payroll withholding can differ based on Form W-4 entries, credits, pre-tax benefit treatment, state/local law, and year-to-date wages.', ToolCalculationWarningLevel::Info),
        ], calculationMemory: $memory);
    }
}
