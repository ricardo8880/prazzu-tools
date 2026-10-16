<?php

declare(strict_types=1);

namespace App\Tools\AdmissionSimulator\Domain\Services;

use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\AdmissionSimulator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public const RULE_VERSION = '1.1.0';

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Incompatible input.');
        }

        $burden = $input->salary->percentage($input->monthlyBurden);
        $recurring = $input->salary->add($input->benefits)->add($burden);
        $oneOff = $input->exam->add($input->recruitment)->add($input->equipment)->add($input->training);
        $firstMonth = $recurring->add($oneOff);
        $firstYear = $recurring->multiply(12)->add($oneOff);

        $memory = new CalculationMemory(
            schemaVersion: self::RULE_VERSION,
            steps: [
                new CalculationMemoryStep('burden', 'Employer payroll burden', 'base wages × user-supplied employer burden rate', ['salary' => $input->salary->minorAmount(), 'monthly_burden' => $input->monthlyBurden->toDecimalString()], $burden->minorAmount(), 'Percentage of wages; monetary values in cents.'),
                new CalculationMemoryStep('recurring', 'Recurring monthly employer cost', 'base wages + benefits + employer burden', ['salary' => $input->salary->minorAmount(), 'benefits' => $input->benefits->minorAmount(), 'burden' => $burden->minorAmount()], $recurring->minorAmount(), 'Monetary values in cents.'),
                new CalculationMemoryStep('one_off', 'One-time hiring costs', 'screening/onboarding + recruiting + equipment + training', ['exam' => $input->exam->minorAmount(), 'recruitment' => $input->recruitment->minorAmount(), 'equipment' => $input->equipment->minorAmount(), 'training' => $input->training->minorAmount()], $oneOff->minorAmount(), 'Monetary values in cents.'),
                new CalculationMemoryStep('first_year', 'First-year projection', '(recurring monthly cost × 12) + one-time hiring costs', ['recurring' => $recurring->minorAmount(), 'one_off' => $oneOff->minorAmount()], $firstYear->minorAmount(), 'Monetary values in cents.'),
            ],
            assumptions: ['The employer burden rate is supplied by the user and should include applicable employer payroll taxes, insurance, and other recurring employer costs.'],
            isEstimate: true,
        );

        return new ToolCalculationResult(
            'new-hire-cost-calculator',
            self::RULE_VERSION,
            [
                new ToolCalculationSummaryItem('first_month', 'First-month employer cost', $firstMonth->formatEnUs()),
                new ToolCalculationSummaryItem('recurring', 'Recurring monthly employer cost', $recurring->formatEnUs()),
                new ToolCalculationSummaryItem('one_off', 'One-time hiring costs', $oneOff->formatEnUs()),
                new ToolCalculationSummaryItem('annual', 'First-year projection', $firstYear->formatEnUs()),
            ],
            [
                'input' => $input->toArray(),
                'checklist' => ['Form I-9 and acceptable identity/work-authorization documents', 'Form W-4', 'Applicable state/local withholding forms', 'Direct-deposit or payroll instructions', 'Offer letter or employment agreement, when used', 'Benefits elections and beneficiary forms', 'Emergency contact information', 'Required workplace notices and policies', 'Background/screening steps only when lawful and applicable', 'E-Verify steps when required or voluntarily used'],
            ],
            calculationMemory: $memory,
        );
    }
}
