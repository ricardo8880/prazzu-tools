<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\CashFlowCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }

        $inflows = $input->salesReceipts->add($input->otherInflows);
        $outflows = $input->operatingPayments->add($input->taxPayments)
            ->add($input->investments)->add($input->financingPayments)->add($input->otherOutflows);
        $netMovement = $inflows->subtract($outflows);
        $closingBalance = $input->openingBalance->add($netMovement);
        $operatingGeneration = $input->salesReceipts
            ->subtract($input->operatingPayments)
            ->subtract($input->taxPayments);

        return new ToolCalculationResult(
            'fluxo-de-caixa',
            '1.0.0',
            [
                new ToolCalculationSummaryItem('closing_balance', 'Saldo final previsto', $closingBalance->formatPtBr()),
                new ToolCalculationSummaryItem('net_movement', 'Movimento líquido do período', $netMovement->formatPtBr()),
                new ToolCalculationSummaryItem('total_inflows', 'Total de entradas', $inflows->formatPtBr()),
                new ToolCalculationSummaryItem('total_outflows', 'Total de saídas', $outflows->formatPtBr()),
                new ToolCalculationSummaryItem('operating_generation', 'Geração operacional de caixa', $operatingGeneration->formatPtBr()),
            ],
            [
                'input' => $input->toArray(),
                'memory' => [
                    'Entradas = recebimentos de vendas + outras entradas' => $inflows->formatPtBr(),
                    'Saídas = pagamentos operacionais + tributos + investimentos + financiamentos + outras saídas' => $outflows->formatPtBr(),
                    'Movimento líquido = entradas - saídas' => $netMovement->formatPtBr(),
                    'Saldo final = saldo inicial + movimento líquido' => $closingBalance->formatPtBr(),
                    'Geração operacional = vendas - pagamentos operacionais - tributos' => $operatingGeneration->formatPtBr(),
                ],
            ],
        );
    }
}
