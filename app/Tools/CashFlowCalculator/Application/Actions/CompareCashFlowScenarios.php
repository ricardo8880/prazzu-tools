<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;

final readonly class CompareCashFlowScenarios
{
    public const FEATURE = 'cash_flow_scenarios';

    public function __construct(private CalculateTool $calculate) {}

    /** @param array<string,string> $data
     *  @return array<int,array{name:string,result:ToolCalculationResult,input:array<string,string>}>
     */
    public function execute(array $data): array
    {
        $base = $this->baseInput($data);
        $inflowRate = Percentage::fromString((string) ($data['inflow_change_rate'] ?? '10'));
        $outflowRate = Percentage::fromString((string) ($data['outflow_change_rate'] ?? '10'));

        $optimistic = $base;
        $conservative = $base;
        foreach (['sales_receipts', 'other_inflows'] as $field) {
            $optimistic[$field] = $this->adjust($base[$field], $inflowRate);
            $conservative[$field] = $this->adjust($base[$field], Percentage::fromString('-'.$inflowRate->toDecimalString()));
        }
        foreach (['operating_payments', 'tax_payments', 'investments', 'financing_payments', 'other_outflows'] as $field) {
            $optimistic[$field] = $this->adjust($base[$field], Percentage::fromString('-'.$outflowRate->toDecimalString()));
            $conservative[$field] = $this->adjust($base[$field], $outflowRate);
        }

        return [
            ['name' => 'Base', 'result' => $this->calculate->execute($base), 'input' => $base],
            ['name' => 'Conservador', 'result' => $this->calculate->execute($conservative), 'input' => $conservative],
            ['name' => 'Otimista', 'result' => $this->calculate->execute($optimistic), 'input' => $optimistic],
        ];
    }

    /** @param array<string,string> $data @return array<string,string> */
    private function baseInput(array $data): array
    {
        $fields = ['opening_balance', 'sales_receipts', 'other_inflows', 'operating_payments', 'tax_payments', 'investments', 'financing_payments', 'other_outflows'];
        $input = [];
        foreach ($fields as $field) {
            $input[$field] = (string) ($data[$field] ?? '0');
        }

        return $input;
    }

    private function adjust(string $amount, Percentage $rate): string
    {
        $money = Money::fromDecimal($amount);
        $adjusted = $money->add($money->percentage($rate));

        return $adjusted->formatPtBr();
    }
}
