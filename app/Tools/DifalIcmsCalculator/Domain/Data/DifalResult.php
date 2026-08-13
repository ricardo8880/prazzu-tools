<?php

declare(strict_types=1);

namespace App\Tools\DifalIcmsCalculator\Domain\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\CalculationMemory;

final readonly class DifalResult
{
    public function __construct(public Money $originIcms, public Money $destinationBase, public Money $destinationIcms, public Money $difal, public Money $fcp, public Money $totalDestination, public Percentage $interstateRate, public CalculationMemory $memory) {}
}
