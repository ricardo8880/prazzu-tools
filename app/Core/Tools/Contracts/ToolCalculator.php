<?php

declare(strict_types=1);

namespace App\Core\Tools\Contracts;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;

interface ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult;
}
