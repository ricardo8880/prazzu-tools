<?php

declare(strict_types=1);

namespace App\Core\Labor\Normative;

use App\Core\Money\Percentage;

final readonly class EmployerChargeRates
{
    public function __construct(
        public Percentage $socialSecurity,
        public Percentage $medicare,
        public Percentage $futa,
        public Percentage $suta,
        public Percentage $otherEmployerCosts,
    ) {}

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'social_security' => $this->socialSecurity->toDecimalString(),
            'medicare' => $this->medicare->toDecimalString(),
            'futa' => $this->futa->toDecimalString(),
            'suta' => $this->suta->toDecimalString(),
            'other_employer_costs' => $this->otherEmployerCosts->toDecimalString(),
        ];
    }
}
