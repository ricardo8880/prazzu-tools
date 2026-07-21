<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Domain\Data;

use App\Core\Money\Money;

final readonly class LatePaymentResult
{
    /** @param list<string> $warnings */
    public function __construct(
        public Money $principal,
        public int $calendarDaysLate,
        public string $penaltyPercent,
        public Money $penalty,
        public string $interestPercent,
        public Money $interest,
        public Money $total,
        public array $warnings,
        /** @var array<string, mixed> */
        public array $normativeRule,
    ) {}
}
