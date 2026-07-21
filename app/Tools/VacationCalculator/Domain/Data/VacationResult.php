<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Domain\Data;

use App\Core\Money\Money;
use DateTimeImmutable;

final readonly class VacationResult
{
    /** @param list<string> $warnings */
    public function __construct(
        public Money $remunerationBase,
        public int $entitledDays,
        public int $leaveDays,
        public int $cashAllowanceDays,
        public Money $vacationRemuneration,
        public Money $vacationThird,
        public Money $cashAllowance,
        public Money $cashAllowanceThird,
        public Money $grossTotal,
        public Money $otherDeductions,
        public Money $netTotal,
        public DateTimeImmutable $acquisitionEndDate,
        public DateTimeImmutable $concessionDeadline,
        public DateTimeImmutable $paymentDeadline,
        public bool $concessionPeriodOverdue,
        public array $warnings,
    ) {}
}
