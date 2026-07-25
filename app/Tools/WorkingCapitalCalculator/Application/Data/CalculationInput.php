<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public Money $cash,
        public Money $receivables,
        public Money $inventory,
        public Money $otherCurrentAssets,
        public Money $suppliers,
        public Money $otherOperatingLiabilities,
        public Money $loans,
        public Money $otherCurrentLiabilities,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'cash' => $this->cash->minorAmount(),
            'receivables' => $this->receivables->minorAmount(),
            'inventory' => $this->inventory->minorAmount(),
            'other_current_assets' => $this->otherCurrentAssets->minorAmount(),
            'suppliers' => $this->suppliers->minorAmount(),
            'other_operating_liabilities' => $this->otherOperatingLiabilities->minorAmount(),
            'loans' => $this->loans->minorAmount(),
            'other_current_liabilities' => $this->otherCurrentLiabilities->minorAmount(),
        ];
    }
}
