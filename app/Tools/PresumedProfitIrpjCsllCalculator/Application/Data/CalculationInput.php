<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Tools\PresumedProfitIrpjCsllCalculator\Domain\Data\PresumedProfitInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public int $quarter,
        public string $commerceRevenue,
        public string $fuelRevenue,
        public string $passengerTransportRevenue,
        public string $servicesRevenue,
        public string $otherTaxableAdditions,
        public string $priorIrpjPresumptionRevenue,
        public string $priorCsllPresumptionRevenue,
        public string $irpjCredits,
        public string $csllCredits,
    ) {}

    public function toDomain(): PresumedProfitInput
    {
        return new PresumedProfitInput(
            quarter: $this->quarter,
            activityRevenue: [
                'commerce_industry' => Money::fromDecimal($this->commerceRevenue),
                'fuel_resale' => Money::fromDecimal($this->fuelRevenue),
                'passenger_transport' => Money::fromDecimal($this->passengerTransportRevenue),
                'services_general' => Money::fromDecimal($this->servicesRevenue),
            ],
            otherTaxableAdditions: Money::fromDecimal($this->otherTaxableAdditions),
            priorIrpjPresumptionRevenue: Money::fromDecimal($this->priorIrpjPresumptionRevenue),
            priorCsllPresumptionRevenue: Money::fromDecimal($this->priorCsllPresumptionRevenue),
            irpjCredits: Money::fromDecimal($this->irpjCredits),
            csllCredits: Money::fromDecimal($this->csllCredits),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
