<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Data;

use App\Core\Money\Money;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Tools\TaxRegimeComparator\Domain\Enums\TaxRegime;
use DateTimeImmutable;

final readonly class TaxComparisonResult
{
    /**
     * @param list<TaxRegimeEstimate> $estimates
     * @param list<string> $assumptions
     * @param list<string> $warnings
     * @param list<RankedTaxRegimeEstimate> $ranking
     * @param list<NormativeRuleSnapshot> $normativeRules
     * @param list<array<string, mixed>> $calculationMemory
     */
    public function __construct(
        public DateTimeImmutable $referenceDate,
        public array $estimates,
        public ?TaxRegime $lowestEstimatedBurden,
        public ?Money $estimatedMonthlySavings,
        public ?Money $estimatedAnnualSavings,
        public array $assumptions = [],
        public array $warnings = [],
        public string $ruleVersion = 'draft',
        public array $ranking = [],
        public int $comparableRegimeCount = 0,
        public array $normativeRules = [],
        public array $calculationMemory = [],
        public bool $isEstimate = true,
    ) {}
}
