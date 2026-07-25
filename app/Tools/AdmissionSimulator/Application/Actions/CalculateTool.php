<?php

declare(strict_types=1);

namespace App\Tools\AdmissionSimulator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\AdmissionSimulator\Application\Data\CalculationInput;
use App\Tools\AdmissionSimulator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(array $d): ToolCalculationResult
    {
        $m = fn ($k) => Money::fromDecimal($d[$k]);

        return $this->calculator->calculate(new CalculationInput($m('salary'), $m('benefits'), Percentage::fromString($d['monthly_burden']), $m('exam'), $m('recruitment'), $m('equipment'), $m('training')));
    }
}
