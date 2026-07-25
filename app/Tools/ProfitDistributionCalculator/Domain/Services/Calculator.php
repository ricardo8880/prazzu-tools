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
            throw new InvalidArgumentException('Entrada incompatível com a calculadora de distribuição de lucros.');
        }

        $result = ($this->calculator ?? new ProfitDistributionCalculator)->calculate($input->toDomain());
        $partner = $result->partners[0];

        return new ToolCalculationResult(
            toolSlug: 'distribuicao-de-lucros',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('available', 'Lucro máximo disponível', $result->maximumAvailableProfit->formatPtBr()),
                new ToolCalculationSummaryItem('distributed', 'Valor distribuído', $result->distributedAmount->formatPtBr()),
                new ToolCalculationSummaryItem('partner', 'Valor do sócio', $partner->distributedAmount->formatPtBr()),
                new ToolCalculationSummaryItem('remaining', 'Saldo não distribuído', $result->undistributedBalance->formatPtBr()),
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
        );
    }
}
