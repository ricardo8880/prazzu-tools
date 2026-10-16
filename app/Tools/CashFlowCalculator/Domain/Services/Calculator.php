<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Domain\Services;

use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\CashFlowCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Input is incompatible with the cash flow calculator.');
        }

        $inflows = $input->salesReceipts->add($input->otherInflows);
        $outflows = $input->operatingPayments->add($input->taxPayments)
            ->add($input->investments)->add($input->financingPayments)->add($input->otherOutflows);
        $netMovement = $inflows->subtract($outflows);
        $closingBalance = $input->openingBalance->add($netMovement);
        $operatingGeneration = $input->salesReceipts
            ->subtract($input->operatingPayments)
            ->subtract($input->taxPayments);

        $warnings = [];
        if ($closingBalance->minorAmount() < 0) {
            $warnings[] = new ToolCalculationWarning(
                code: 'negative_closing_balance',
                message: 'Projected ending cash is negative. Review the timing of receipts and payments or consider additional cash needs for the period.',
                level: ToolCalculationWarningLevel::Danger,
                title: 'Negative ending cash',
            );
        }
        if ($operatingGeneration->minorAmount() < 0) {
            $warnings[] = new ToolCalculationWarning(
                code: 'negative_operating_generation',
                message: 'Sales receipts do not cover operating payments and taxes for the period. Investing and financing cash flows are excluded from this operating indicator.',
                level: ToolCalculationWarningLevel::Warning,
                title: 'Negative operating cash generation',
            );
        }

        return new ToolCalculationResult(
            toolSlug: 'cash-flow-calculator',
            schemaVersion: '1.2.0',
            summary: [
                new ToolCalculationSummaryItem('closing_balance', 'Projected ending cash', $closingBalance->formatEnUs()),
                new ToolCalculationSummaryItem('net_movement', 'Net cash movement', $netMovement->formatEnUs()),
                new ToolCalculationSummaryItem('total_inflows', 'Total inflows', $inflows->formatEnUs()),
                new ToolCalculationSummaryItem('total_outflows', 'Total outflows', $outflows->formatEnUs()),
                new ToolCalculationSummaryItem('operating_generation', 'Operating cash generation', $operatingGeneration->formatEnUs(), 'Sales receipts minus operating payments and taxes.'),
            ],
            details: ['input' => $input->toArray()],
            warnings: $warnings,
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.2.0',
                steps: [
                    new CalculationMemoryStep('total_inflows', 'Total inflows', 'sales receipts + other inflows', ['sales_receipts' => $input->salesReceipts->minorAmount(), 'other_inflows' => $input->otherInflows->minorAmount()], $inflows->minorAmount(), 'Calculated in cents without floating point.'),
                    new CalculationMemoryStep('total_outflows', 'Total outflows', 'operating payments + taxes + investments + financing payments + other outflows', ['operating_payments' => $input->operatingPayments->minorAmount(), 'tax_payments' => $input->taxPayments->minorAmount(), 'investments' => $input->investments->minorAmount(), 'financing_payments' => $input->financingPayments->minorAmount(), 'other_outflows' => $input->otherOutflows->minorAmount()], $outflows->minorAmount(), 'Calculated in cents without floating point.'),
                    new CalculationMemoryStep('net_movement', 'Net cash movement', 'total inflows − total outflows', ['total_inflows' => $inflows->minorAmount(), 'total_outflows' => $outflows->minorAmount()], $netMovement->minorAmount()),
                    new CalculationMemoryStep('closing_balance', 'Projected ending cash', 'opening cash + net cash movement', ['opening_balance' => $input->openingBalance->minorAmount(), 'net_movement' => $netMovement->minorAmount()], $closingBalance->minorAmount()),
                    new CalculationMemoryStep('operating_generation', 'Operating cash generation', 'sales receipts − operating payments − taxes', ['sales_receipts' => $input->salesReceipts->minorAmount(), 'operating_payments' => $input->operatingPayments->minorAmount(), 'tax_payments' => $input->taxPayments->minorAmount()], $operatingGeneration->minorAmount()),
                ],
                assumptions: [
                    'Opening cash and cash movements must refer to the same projection period.',
                    'The tool uses a cash basis: it considers receipt and payment timing rather than accrual accounting.',
                    'Ending cash is a projection based only on the entered cash movements and does not include unrecorded events.',
                ],
                isEstimate: true,
            ),
        );
    }
}
