<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data;

use App\Core\Money\Money;

final readonly class ProLaboreResult
{
    /** @param list<array<string, mixed>> $memory */
    /** @param list<array<string, mixed>> $normativeRules */
    public function __construct(
        public Money $grossAmount,
        public Money $socialSecurityBase,
        public Money $socialSecurityWithheld,
        public Money $legalIrrfDeductions,
        public Money $simplifiedIrrfDeduction,
        public string $irrfDeductionMethod,
        public Money $irrfBase,
        public Money $irrfBeforeReduction,
        public Money $irrfReduction,
        public Money $irrfWithheld,
        public Money $netAmount,
        public Money $employerContribution,
        public Money $companyTotalCost,
        public array $memory,
        public array $normativeRules,
    ) {}
}
