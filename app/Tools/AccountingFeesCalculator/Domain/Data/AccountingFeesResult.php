<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Data;

use App\Core\Money\Money;

final readonly class AccountingFeesResult
{
    /**
     * @param array<int, array{label: string, value: Money, percentage: int}> $breakdown
     * @param array<int, array{label: string, percentage: int}> $appliedFactors
     * @param array<int, array{icon: string, title: string, description: string}> $recommendations
     */
    public function __construct(
        public Money $minimumFee,
        public Money $recommendedFee,
        public Money $upperReferenceFee,
        public int $complexityScore,
        public string $complexityLevel,
        public array $breakdown,
        public array $appliedFactors,
        public array $recommendations,
        public string $ruleVersion,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'minimum_fee' => $this->minimumFee->formatPtBr(),
            'recommended_fee' => $this->recommendedFee->formatPtBr(),
            'upper_reference_fee' => $this->upperReferenceFee->formatPtBr(),
            'complexity_score' => $this->complexityScore,
            'complexity_level' => $this->complexityLevel,
            'breakdown' => array_map(
                static fn (array $item): array => [
                    'label' => $item['label'],
                    'value' => $item['value']->formatPtBr(),
                    'percentage' => $item['percentage'],
                ],
                $this->breakdown,
            ),
            'applied_factors' => $this->appliedFactors,
            'recommendations' => $this->recommendations,
            'rule_version' => $this->ruleVersion,
        ];
    }
}
