<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Domain\Rules;

use App\Core\Money\Money;
use App\Core\Money\Percentage;

final readonly class SocialSecurityBracket
{
    public function __construct(
        public Money $lowerLimit,
        public Money $upperLimit,
        public Percentage $rate,
    ) {}
}
