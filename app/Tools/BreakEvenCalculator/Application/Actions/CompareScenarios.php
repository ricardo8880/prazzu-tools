<?php

declare(strict_types=1);

namespace App\Tools\BreakEvenCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;

final readonly class CompareScenarios
{
    public const FEATURE = 'scenario_comparison';

    public function __construct(private CalculateTool $calculate) {}

    /** @param array<string,string> $data
     *  @return array<int,array{name:string,result:ToolCalculationResult,input:array<string,string>}>
     */
    public function execute(array $data): array
    {
        $base = [
            'fixed_costs' => (string) $data['fixed_costs'],
            'sale_price' => (string) $data['sale_price'],
            'variable_cost' => (string) $data['variable_cost'],
        ];
        $scenario = [
            'fixed_costs' => $this->adjust($base['fixed_costs'], (string) ($data['fixed_cost_change_rate'] ?? '0')),
            'sale_price' => $this->adjust($base['sale_price'], (string) ($data['sale_price_change_rate'] ?? '0')),
            'variable_cost' => $this->adjust($base['variable_cost'], (string) ($data['variable_cost_change_rate'] ?? '0')),
        ];

        return [
            ['name' => 'Base', 'result' => $this->calculate->execute($base), 'input' => $base],
            ['name' => trim((string) ($data['scenario_name'] ?? 'Cenário alternativo')) ?: 'Cenário alternativo', 'result' => $this->calculate->execute($scenario), 'input' => $scenario],
        ];
    }

    private function adjust(string $amount, string $rate): string
    {
        $money = Money::fromDecimal($amount);
        $adjusted = $money->add($money->percentage(Percentage::fromString($rate)));

        return $adjusted->formatPtBr();
    }
}
