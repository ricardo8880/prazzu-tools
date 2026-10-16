<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Domain\Services;

use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\PayslipGenerator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public const RULE_VERSION = '2.0.0';

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) { throw new InvalidArgumentException('Incompatible input.'); }

        $earnings = $input->regularPay->add($input->overtimePay)->add($input->otherEarnings);
        $deductions = $input->federalIncomeTax
            ->add($input->socialSecurity)
            ->add($input->medicare)
            ->add($input->stateLocalTax)
            ->add($input->otherDeductions);
        if ($deductions->minorAmount() > $earnings->minorAmount()) {
            throw new InvalidArgumentException('Deductions cannot exceed gross earnings.');
        }
        $net = $earnings->subtract($deductions);

        return new ToolCalculationResult(
            'pay-stub-generator',
            self::RULE_VERSION,
            [
                new ToolCalculationSummaryItem('gross', 'Gross earnings', $earnings->formatEnUs()),
                new ToolCalculationSummaryItem('deductions', 'Total deductions', $deductions->formatEnUs()),
                new ToolCalculationSummaryItem('net', 'Net pay', $net->formatEnUs()),
            ],
            [
                'input' => $input->toArray(),
                'document' => [
                    'employee' => $input->employeeName,
                    'employee_id' => $input->employeeId,
                    'employer' => $input->employer,
                    'pay_period' => $input->payPeriod,
                    'items' => [
                        'Regular pay' => $input->regularPay->formatEnUs(),
                        'Overtime pay' => $input->overtimePay->formatEnUs(),
                        'Other earnings' => $input->otherEarnings->formatEnUs(),
                        'Federal income tax' => '-'.$input->federalIncomeTax->formatEnUs(),
                        'Social Security' => '-'.$input->socialSecurity->formatEnUs(),
                        'Medicare' => '-'.$input->medicare->formatEnUs(),
                        'State/local tax' => '-'.$input->stateLocalTax->formatEnUs(),
                        'Other deductions' => '-'.$input->otherDeductions->formatEnUs(),
                    ],
                    'net' => $net->formatEnUs(),
                ],
            ],
            warnings: [new ToolCalculationWarning('payroll_input', 'This generator organizes amounts you enter. It does not replace payroll software or determine federal, state, or local withholding requirements.')],
            calculationMemory: new CalculationMemory(self::RULE_VERSION, [
                new CalculationMemoryStep('earnings', 'Gross earnings', 'regular pay + overtime + other earnings', [], $earnings->minorAmount()),
                new CalculationMemoryStep('deductions', 'Total deductions', 'federal income tax + Social Security + Medicare + state/local tax + other deductions', [], $deductions->minorAmount()),
                new CalculationMemoryStep('net', 'Net pay', 'gross earnings - deductions', [], $net->minorAmount()),
            ], assumptions: ['All withholding amounts are supplied by the user.']),
        );
    }
}
