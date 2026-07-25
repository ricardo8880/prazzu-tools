<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\CashFlowCalculator\Application\Data\CalculationInput;
use App\Tools\CashFlowCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(array $data): ToolCalculationResult
    {
        $money = static fn (string $key): Money => Money::fromDecimal($data[$key]);

        return $this->calculator->calculate(new CalculationInput(
            $money('opening_balance'), $money('sales_receipts'), $money('other_inflows'),
            $money('operating_payments'), $money('tax_payments'), $money('investments'),
            $money('financing_payments'), $money('other_outflows'),
        ));
    }
}
