<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\ProfitDistributionCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final readonly class Calculator implements ToolCalculator
{
    public function __construct(private ?ProfitDistributionCalculator $calculator = null) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Incompatible input for the owner distribution calculator.');
        }

        $result = ($this->calculator ?? new ProfitDistributionCalculator)->calculate($input->toDomain());
        $partner = $result->partners[0];

        return new ToolCalculationResult(
            toolSlug: 'owner-distribution-calculator',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('available', 'Maximum available amount', $result->maximumAvailableProfit->formatEnUs()),
                new ToolCalculationSummaryItem('distributed', 'Planned distribution', $result->distributedAmount->formatEnUs()),
                new ToolCalculationSummaryItem('partner', 'Primary owner amount', $partner->distributedAmount->formatEnUs()),
                new ToolCalculationSummaryItem('remaining', 'Undistributed balance', $result->undistributedBalance->formatEnUs()),
            ],
            details: [
                'input' => $input->toArray(),
                'accounting_profit_minor' => $result->accountingProfit->minorAmount(),
                'maximum_available_profit_minor' => $result->maximumAvailableProfit->minorAmount(),
                'distributed_amount_minor' => $result->distributedAmount->minorAmount(),
                'undistributed_balance_minor' => $result->undistributedBalance->minorAmount(),
                'partners' => array_map(static fn ($item): array => [
                    'key' => $item->key,
                    'label' => $item->label,
                    'ownership_percentage' => $item->ownershipPercentage->toDecimalString(),
                    'distributed_amount_minor' => $item->distributedAmount->minorAmount(),
                ], $result->partners),
                'memory' => $result->memory,
                'warnings' => $result->warnings,
            ],
            warnings: [
                new \App\Core\Tools\Calculation\Data\ToolCalculationWarning('entity_tax', 'Tax treatment of owner distributions depends on the entity type and the owner’s tax situation. This calculator does not determine whether a payment is a dividend, partnership distribution, S-corporation distribution, compensation, or yearther tax category.'),
                new \App\Core\Tools\Calculation\Data\ToolCalculationWarning('legal_capacity', 'Confirm governing documents, state-law distribution restrictions, solvency, lender covenants, and accounting records before making a distribution.'),
            ],
        );
    }
}
