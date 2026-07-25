<?php

declare(strict_types=1);

namespace App\Core\Labor\Normative;

use App\Core\Money\Percentage;

final readonly class EmployerChargeRates
{
    public function __construct(
        public Percentage $fgts,
        public Percentage $cpp,
        public Percentage $rat,
        public Percentage $thirdParties,
    ) {}

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'fgts' => $this->fgts->toDecimalString(),
            'cpp' => $this->cpp->toDecimalString(),
            'rat' => $this->rat->toDecimalString(),
            'third_parties' => $this->thirdParties->toDecimalString(),
        ];
    }
}
