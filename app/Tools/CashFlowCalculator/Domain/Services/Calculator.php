<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Domain\Services;

use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\CashFlowCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a calculadora de fluxo de caixa.');
        }

        $inflows = $input->salesReceipts->add($input->otherInflows);
        $outflows = $input->operatingPayments->add($input->taxPayments)
            ->add($input->investments)->add($input->financingPayments)->add($input->otherOutflows);
        $netMovement = $inflows->subtract($outflows);
        $closingBalance = $input->openingBalance->add($netMovement);
        $operatingGeneration = $input->salesReceipts
            ->subtract($input->operatingPayments)
            ->subtract($input->taxPayments);

        $warnings = [];
        if ($closingBalance->minorAmount() < 0) {
            $warnings[] = new ToolCalculationWarning(
                code: 'negative_closing_balance',
                message: 'O saldo final projetado ficou negativo. Revise o calendário de recebimentos e pagamentos ou considere necessidade de caixa adicional para o período.',
                level: ToolCalculationWarningLevel::Danger,
                title: 'Saldo final negativo',
            );
        }
        if ($operatingGeneration->minorAmount() < 0) {
            $warnings[] = new ToolCalculationWarning(
                code: 'negative_operating_generation',
                message: 'Os recebimentos de vendas informados não cobrem pagamentos operacionais e tributos do período. Investimentos e financiamentos não entram neste indicador.',
                level: ToolCalculationWarningLevel::Warning,
                title: 'Geração operacional negativa',
            );
        }

        return new ToolCalculationResult(
            toolSlug: 'fluxo-de-caixa',
            schemaVersion: '1.2.0',
            summary: [
                new ToolCalculationSummaryItem('closing_balance', 'Saldo final previsto', $closingBalance->formatPtBr()),
                new ToolCalculationSummaryItem('net_movement', 'Movimento líquido do período', $netMovement->formatPtBr()),
                new ToolCalculationSummaryItem('total_inflows', 'Total de entradas', $inflows->formatPtBr()),
                new ToolCalculationSummaryItem('total_outflows', 'Total de saídas', $outflows->formatPtBr()),
                new ToolCalculationSummaryItem('operating_generation', 'Geração operacional de caixa', $operatingGeneration->formatPtBr(), 'Recebimentos de vendas menos pagamentos operacionais e tributos.'),
            ],
            details: ['input' => $input->toArray()],
            warnings: $warnings,
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.2.0',
                steps: [
                    new CalculationMemoryStep('total_inflows', 'Total de entradas', 'recebimentos de vendas + outras entradas', ['sales_receipts' => $input->salesReceipts->minorAmount(), 'other_inflows' => $input->otherInflows->minorAmount()], $inflows->minorAmount(), 'Soma em centavos, sem ponto flutuante.'),
                    new CalculationMemoryStep('total_outflows', 'Total de saídas', 'pagamentos operacionais + tributos + investimentos + financiamentos + outras saídas', ['operating_payments' => $input->operatingPayments->minorAmount(), 'tax_payments' => $input->taxPayments->minorAmount(), 'investments' => $input->investments->minorAmount(), 'financing_payments' => $input->financingPayments->minorAmount(), 'other_outflows' => $input->otherOutflows->minorAmount()], $outflows->minorAmount(), 'Soma em centavos, sem ponto flutuante.'),
                    new CalculationMemoryStep('net_movement', 'Movimento líquido do período', 'total de entradas − total de saídas', ['total_inflows' => $inflows->minorAmount(), 'total_outflows' => $outflows->minorAmount()], $netMovement->minorAmount()),
                    new CalculationMemoryStep('closing_balance', 'Saldo final previsto', 'saldo inicial + movimento líquido', ['opening_balance' => $input->openingBalance->minorAmount(), 'net_movement' => $netMovement->minorAmount()], $closingBalance->minorAmount()),
                    new CalculationMemoryStep('operating_generation', 'Geração operacional de caixa', 'recebimentos de vendas − pagamentos operacionais − tributos', ['sales_receipts' => $input->salesReceipts->minorAmount(), 'operating_payments' => $input->operatingPayments->minorAmount(), 'tax_payments' => $input->taxPayments->minorAmount()], $operatingGeneration->minorAmount()),
                ],
                assumptions: [
                    'Saldo inicial e movimentações devem corresponder ao mesmo período de projeção.',
                    'A ferramenta usa regime de caixa: considera datas de recebimento e pagamento, não competência contabilística.',
                    'O saldo final é uma previsão baseada exclusivamente nos movimentos informados e não inclui eventos não lançados.',
                ],
                isEstimate: true,
            ),
        );
    }
}
