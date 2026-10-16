<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\WorkingCapitalCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Incompatible input for the working capital calculator.');
        }

        $operatingAssets = $input->receivables->add($input->inventory)->add($input->otherCurrentAssets);
        $operatingLiabilities = $input->suppliers->add($input->otherOperatingLiabilities);
        $need = $operatingAssets->subtract($operatingLiabilities);
        $currentAssets = $input->cash->add($operatingAssets);
        $currentLiabilities = $operatingLiabilities->add($input->loans)->add($input->otherCurrentLiabilities);
        $netWorkingCapital = $currentAssets->subtract($currentLiabilities);
        $requiredCapital = Money::fromMinor(max(0, $need->minorAmount()));
        $fundingGap = Money::fromMinor(max(0, $requiredCapital->minorAmount() - $netWorkingCapital->minorAmount()));
        $fundingSurplus = Money::fromMinor(max(0, $netWorkingCapital->minorAmount() - $requiredCapital->minorAmount()));

        $warnings = [];
        if ($fundingGap->minorAmount() > 0) {
            $warnings[] = new ToolCalculationWarning(
                code: 'funding_gap',
                message: 'Entered net working capital does not fully cover the estimated operating need. The displayed difference represents the funding gap at this point in time.',
                level: ToolCalculationWarningLevel::Warning,
                title: 'Additional funding is required',
            );
        } elseif ($fundingSurplus->minorAmount() > 0) {
            $warnings[] = new ToolCalculationWarning(
                code: 'funding_surplus',
                message: 'Net working capital exceeds the estimated operating need. The surplus does not replace liquidity, seasonality, or average-term analysis.',
                level: ToolCalculationWarningLevel::Info,
                title: 'There is a working capital surplus',
            );
        }

        return new ToolCalculationResult(
            toolSlug: 'working-capital-calculator',
            schemaVersion: '1.2.0',
            summary: [
                new ToolCalculationSummaryItem('required_capital', 'Required working capital', $requiredCapital->formatEnUs(), 'Positive operating need that must be funded.'),
                new ToolCalculationSummaryItem('operating_need', 'Operating working capital need', $need->formatEnUs(), 'Operating assets minus operating liabilities.'),
                new ToolCalculationSummaryItem('net_working_capital', 'Net working capital (NWC)', $netWorkingCapital->formatEnUs(), 'Current assets minus current liabilities.'),
                new ToolCalculationSummaryItem('funding_gap', 'Additional funding required', $fundingGap->formatEnUs(), 'Operating need not yet covered by net working capital.'),
                new ToolCalculationSummaryItem('funding_surplus', 'Working capital surplus', $fundingSurplus->formatEnUs(), 'Net working capital above the estimated operating need.'),
            ],
            details: ['input' => $input->toArray()],
            warnings: $warnings,
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.2.0',
                steps: [
                    new CalculationMemoryStep('operating_assets', 'Operating assets', 'accounts receivable + inventory + other operating current assets', ['receivables' => $input->receivables->minorAmount(), 'inventory' => $input->inventory->minorAmount(), 'other_current_assets' => $input->otherCurrentAssets->minorAmount()], $operatingAssets->minorAmount(), 'Sum in cents, without floating point.'),
                    new CalculationMemoryStep('operating_liabilities', 'Operating liabilities', 'accounts payable + other operating liabilities', ['suppliers' => $input->suppliers->minorAmount(), 'other_operating_liabilities' => $input->otherOperatingLiabilities->minorAmount()], $operatingLiabilities->minorAmount(), 'Sum in cents, without floating point.'),
                    new CalculationMemoryStep('operating_need', 'Operating working capital need', 'operating assets − operating liabilities', ['operating_assets' => $operatingAssets->minorAmount(), 'operating_liabilities' => $operatingLiabilities->minorAmount()], $need->minorAmount()),
                    new CalculationMemoryStep('current_assets', 'Current assets', 'cash + operating assets', ['cash' => $input->cash->minorAmount(), 'operating_assets' => $operatingAssets->minorAmount()], $currentAssets->minorAmount()),
                    new CalculationMemoryStep('current_liabilities', 'Current liabilities', 'operating liabilities + short-term debt + other current liabilities', ['operating_liabilities' => $operatingLiabilities->minorAmount(), 'loans' => $input->loans->minorAmount(), 'other_current_liabilities' => $input->otherCurrentLiabilities->minorAmount()], $currentLiabilities->minorAmount()),
                    new CalculationMemoryStep('net_working_capital', 'Net working capital (NWC)', 'current assets − current liabilities', ['current_assets' => $currentAssets->minorAmount(), 'current_liabilities' => $currentLiabilities->minorAmount()], $netWorkingCapital->minorAmount()),
                    new CalculationMemoryStep('required_capital', 'Required working capital', 'max(0, operating working capital need)', ['operating_need' => $need->minorAmount()], $requiredCapital->minorAmount()),
                    new CalculationMemoryStep('funding_gap', 'Additional funding required', 'max(0, required working capital − NWC)', ['required_capital' => $requiredCapital->minorAmount(), 'net_working_capital' => $netWorkingCapital->minorAmount()], $fundingGap->minorAmount()),
                    new CalculationMemoryStep('funding_surplus', 'Working capital surplus', 'max(0, NWC − required working capital)', ['required_capital' => $requiredCapital->minorAmount(), 'net_working_capital' => $netWorkingCapital->minorAmount()], $fundingSurplus->minorAmount()),
                ],
                assumptions: [
                    'All balances must represent the same reporting date and accounting scope.',
                    'The classification between operating and financing items is supplied by the user and is not automatically reclassified.',
                    'The result is a point-in-time financial snapshot; seasonality, average terms, and subsequent events require additional scenarios.',
                ],
                isEstimate: true,
            ),
        );
    }
}
