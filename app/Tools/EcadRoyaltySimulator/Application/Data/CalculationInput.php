<?php

declare(strict_types=1);

namespace App\Tools\EcadRoyaltySimulator\Application\Data;

use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $method,
        public string $udaValue,
        public ?string $udaQuantity = null,
        public ?string $areaSquareMeters = null,
        public ?string $udaPerSquareMeter = null,
        public ?string $referenceAmount = null,
        public ?string $percentageRate = null,
        public int $periods = 1,
    ) {}

    public function toArray(): array
    {
        return [
            'method' => $this->method,
            'uda_value' => $this->udaValue,
            'uda_quantity' => $this->udaQuantity,
            'area_square_meters' => $this->areaSquareMeters,
            'uda_per_square_meter' => $this->udaPerSquareMeter,
            'reference_amount' => $this->referenceAmount,
            'percentage_rate' => $this->percentageRate,
            'periods' => $this->periods,
        ];
    }
}
