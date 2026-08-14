<?php

declare(strict_types=1);

namespace App\Tools\ActualProfitCalculator\Application\Data;

use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(public string $accountingProfit, public string $additions='0', public string $exclusions='0', public string $irpjLossBalance='0', public string $csllNegativeBalance='0', public int $months=3) {}
    public function toArray(): array { return ['accounting_profit'=>$this->accountingProfit,'additions'=>$this->additions,'exclusions'=>$this->exclusions,'irpj_loss_balance'=>$this->irpjLossBalance,'csll_negative_balance'=>$this->csllNegativeBalance,'months'=>$this->months]; }
}
