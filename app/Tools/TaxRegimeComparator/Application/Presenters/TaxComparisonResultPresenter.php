<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Application\Presenters;

use App\Tools\TaxRegimeComparator\Domain\Data\TaxComparisonResult;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxRegimeEstimate;

final readonly class TaxComparisonResultPresenter
{
    /** @return array<string, mixed> */
    public function present(TaxComparisonResult $result): array
    {
        return [
            'reference_date' => $result->referenceDate->format('d/m/Y'),
            'winner' => $result->lowestEstimatedBurden?->label(),
            'monthly_savings' => $result->estimatedMonthlySavings?->formatPtBr(),
            'annual_savings' => $result->estimatedAnnualSavings?->formatPtBr(),
            'comparable_regime_count' => $result->comparableRegimeCount,
            'rule_version' => $result->ruleVersion,
            'ranking' => array_map(fn ($ranked): array => [
                'position' => $ranked->position,
                'regime' => $ranked->estimate->regime->label(),
                'monthly_tax' => $ranked->estimate->estimatedMonthlyTax?->formatPtBr(),
                'annual_tax' => $ranked->estimate->estimatedAnnualTax?->formatPtBr(),
                'monthly_difference' => $ranked->monthlyDifferenceFromLowest->formatPtBr(),
                'annual_difference' => $ranked->annualDifferenceFromLowest->formatPtBr(),
                'taxes' => array_map(fn ($tax): array => [
                    'name' => $tax->label,
                    'monthly_amount' => $tax->monthlyAmount->formatPtBr(),
                    'annual_amount' => $tax->annualAmount->formatPtBr(),
                ], $ranked->estimate->taxes),
                'assumptions' => $ranked->estimate->assumptions,
                'warnings' => $ranked->estimate->warnings,
            ], $result->ranking),
            'unavailable' => array_values(array_map(
                fn (TaxRegimeEstimate $estimate): array => [
                    'regime' => $estimate->regime->label(),
                    'status' => $estimate->status->value,
                    'warnings' => $estimate->warnings,
                ],
                array_filter($result->estimates, fn (TaxRegimeEstimate $estimate): bool => ! $estimate->isComparable()),
            )),
            'assumptions' => $result->assumptions,
            'warnings' => $result->warnings,
        ];
    }
}
