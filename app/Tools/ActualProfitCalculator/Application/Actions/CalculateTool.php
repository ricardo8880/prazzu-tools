<?php

declare(strict_types=1);

namespace App\Tools\ActualProfitCalculator\Application\Actions;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\ActualProfitCalculator\Application\Data\CalculationInput;
use App\Tools\ActualProfitCalculator\Domain\Services\Calculator;

final readonly class CalculateTool { public function __construct(private Calculator $calculator) {} public function execute(CalculationInput $input): ToolCalculationResult { return $this->calculator->calculate($input); } }
