<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Services;

use App\Core\Money\Money;
use App\Tools\TaxRegimeComparator\Domain\Data\RankedTaxRegimeEstimate;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxComparisonResult;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxRegimeEstimate;
use DateTimeImmutable;

final class TaxComparisonRanker
{
    /**
     * @param list<TaxRegimeEstimate> $estimates
     */
    public function rank(DateTimeImmutable $referenceDate, array $estimates): TaxComparisonResult
    {
        $comparable = array_values(array_filter(
            $estimates,
            static fn (TaxRegimeEstimate $estimate): bool => $estimate->isComparable(),
        ));

        usort(
            $comparable,
            static fn (TaxRegimeEstimate $left, TaxRegimeEstimate $right): int =>
                $left->estimatedAnnualTax->minorAmount() <=> $right->estimatedAnnualTax->minorAmount(),
        );

        $ranking = $this->buildRanking($comparable);
        $hasEffectiveComparison = count($comparable) >= 2;
        $lowest = $hasEffectiveComparison ? $comparable[0] : null;
        $secondLowest = $hasEffectiveComparison ? $comparable[1] : null;

        $warnings = $this->collectWarnings($estimates);

        if (! $hasEffectiveComparison) {
            $warnings[] = count($comparable) === 1
                ? 'Apenas um regime possui estimativa comparável; não é possível apontar economia entre alternativas.'
                : 'Nenhum regime possui estimativa comparável para o cenário informado.';
        }

        return new TaxComparisonResult(
            referenceDate: $referenceDate,
            estimates: $estimates,
            lowestEstimatedBurden: $lowest?->regime,
            estimatedMonthlySavings: $lowest !== null && $secondLowest !== null
                ? $secondLowest->estimatedMonthlyTax->subtract($lowest->estimatedMonthlyTax)
                : null,
            estimatedAnnualSavings: $lowest !== null && $secondLowest !== null
                ? $secondLowest->estimatedAnnualTax->subtract($lowest->estimatedAnnualTax)
                : null,
            assumptions: $this->collectAssumptions($estimates),
            warnings: array_values(array_unique($warnings)),
            ruleVersion: '0.6.0',
            ranking: $ranking,
            comparableRegimeCount: count($comparable),
        );
    }

    /**
     * @param list<TaxRegimeEstimate> $comparable
     * @return list<RankedTaxRegimeEstimate>
     */
    private function buildRanking(array $comparable): array
    {
        if ($comparable === []) {
            return [];
        }

        $lowestMonthly = $comparable[0]->estimatedMonthlyTax;
        $lowestAnnual = $comparable[0]->estimatedAnnualTax;

        return array_map(
            static fn (TaxRegimeEstimate $estimate, int $index): RankedTaxRegimeEstimate => new RankedTaxRegimeEstimate(
                position: $index + 1,
                estimate: $estimate,
                monthlyDifferenceFromLowest: $estimate->estimatedMonthlyTax->subtract($lowestMonthly),
                annualDifferenceFromLowest: $estimate->estimatedAnnualTax->subtract($lowestAnnual),
            ),
            $comparable,
            array_keys($comparable),
        );
    }

    /** @param list<TaxRegimeEstimate> $estimates */
    private function collectAssumptions(array $estimates): array
    {
        $assumptions = [];

        foreach ($estimates as $estimate) {
            foreach ($estimate->assumptions as $assumption) {
                $assumptions[] = $estimate->regime->label().': '.$assumption;
            }
        }

        return array_values(array_unique($assumptions));
    }

    /** @param list<TaxRegimeEstimate> $estimates */
    private function collectWarnings(array $estimates): array
    {
        $warnings = [];

        foreach ($estimates as $estimate) {
            foreach ($estimate->warnings as $warning) {
                $warnings[] = $estimate->regime->label().': '.$warning;
            }
        }

        return array_values(array_unique($warnings));
    }
}
