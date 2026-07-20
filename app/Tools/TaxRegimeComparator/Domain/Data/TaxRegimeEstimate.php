<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Data;

use App\Core\Money\Money;
use App\Tools\TaxRegimeComparator\Domain\Enums\EstimateStatus;
use App\Tools\TaxRegimeComparator\Domain\Enums\TaxRegime;
use App\Tools\TaxRegimeComparator\Domain\Validators\TaxRegimeEstimateValidator;

final readonly class TaxRegimeEstimate
{
    /**
     * @param list<TaxItemEstimate> $taxes
     * @param list<string> $assumptions
     * @param list<string> $warnings
     */
    public function __construct(
        public TaxRegime $regime,
        public EstimateStatus $status,
        public ?Money $estimatedMonthlyTax,
        public ?Money $estimatedAnnualTax,
        public array $taxes = [],
        public array $assumptions = [],
        public array $warnings = [],
    ) {
        (new TaxRegimeEstimateValidator)->validate($this);
    }

    public function isComparable(): bool
    {
        return $this->status->isComparable()
            && $this->estimatedMonthlyTax !== null
            && $this->estimatedAnnualTax !== null;
    }
}
