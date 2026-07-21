<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Domain\Services;

use App\Tools\FederalPaymentGuideGenerator\Domain\Enums\DueDateAdjustment;
use DateTimeImmutable;

final class GpsDueDateCalculator
{
    public function __construct(private readonly WeekendBusinessCalendar $calendar = new WeekendBusinessCalendar()) {}

    public function companyMonthly(DateTimeImmutable $competence): DateTimeImmutable
    {
        $candidate = $competence->modify('first day of next month')->setDate(
            (int) $competence->modify('first day of next month')->format('Y'),
            (int) $competence->modify('first day of next month')->format('m'),
            20,
        );

        return $this->calendar->adjust($candidate, DueDateAdjustment::PreviousBusinessDay);
    }

    public function individualMonthly(DateTimeImmutable $competence): DateTimeImmutable
    {
        $next = $competence->modify('first day of next month');
        $candidate = $next->setDate((int) $next->format('Y'), (int) $next->format('m'), 15);

        return $this->calendar->adjust($candidate, DueDateAdjustment::NextBusinessDay);
    }

    public function companyThirteenthSalary(int $year): DateTimeImmutable
    {
        return $this->calendar->adjust(new DateTimeImmutable(sprintf('%d-12-20', $year)), DueDateAdjustment::PreviousBusinessDay);
    }
}
