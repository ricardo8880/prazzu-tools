<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Domain\Services;

use App\Tools\FederalPaymentGuideGenerator\Domain\Enums\DueDateAdjustment;
use DateTimeImmutable;

final class WeekendBusinessCalendar
{
    public function adjust(DateTimeImmutable $date, DueDateAdjustment $adjustment): DateTimeImmutable
    {
        if ($adjustment === DueDateAdjustment::None) {
            return $date;
        }

        $step = $adjustment === DueDateAdjustment::PreviousBusinessDay ? '-1 day' : '+1 day';
        $adjusted = $date;

        while (in_array((int) $adjusted->format('N'), [6, 7], true)) {
            $adjusted = $adjusted->modify($step);
        }

        return $adjusted;
    }
}
