<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Domain\Data;

use App\Core\Dates\Competence;
use App\Core\Money\Money;
use App\Core\Money\Percentage;

final readonly class OvertimeInput
{
    public function __construct(
        public Competence $competence,
        public Money $baseSalary,
        public int $monthlyHours,
        public int $overtime50Thousandths,
        public int $overtime100Thousandths,
        public int $customOvertimeThousandths,
        public Percentage $customPremium,
        public int $nightClockThousandths,
        public int $nightOvertimeThousandths,
        public Percentage $nightOvertimePremium,
        public int $workingDays,
        public int $restDays,
        public bool $includeDsr,
        public bool $includeReflexes,
    ) {}
}
