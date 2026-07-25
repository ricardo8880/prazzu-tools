<?php

declare(strict_types=1);

namespace App\Tools\BreakEvenCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\BreakEvenCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a calculadora de ponto de equilíbrio.');
        }

        $price = $input->salePrice->minorAmount();
        $contribution = $price - $input->variableCost->minorAmount();
        if ($price <= 0 || $contribution <= 0) {
            throw new InvalidArgumentException('O preço deve ser positivo e maior que o custo variável.');
        }

        $fixed = $input->fixedCosts->minorAmount();
        $units = intdiv($fixed + $contribution - 1, $contribution);
        if ($units > intdiv(PHP_INT_MAX, $price)) {
            throw new InvalidArgumentException('O faturamento calculado excede o intervalo monetário suportado.');
        }

        $revenue = Money::fromMinor($units * $price);
        $marginBasisPoints = intdiv($contribution * 10000, $price);
        $unitContribution = Money::fromMinor($contribution);
        $formattedMargin = number_format($marginBasisPoints / 100, 2, ',', '.').' %';

        return new ToolCalculationResult(
            toolSlug: 'ponto-de-equilibrio',
            schemaVersion: '1.1.0',
            summary: [
                new ToolCalculationSummaryItem('break_even_revenue', 'Faturamento mínimo', $revenue->formatPtBr(), 'Faturamento na primeira unidade inteira que cobre os custos.'),
                new ToolCalculationSummaryItem('break_even_units', 'Quantidade mínima', $units.' unidade'.($units === 1 ? '' : 's')),
                new ToolCalculationSummaryItem('unit_contribution', 'Margem de contribuição unitária', $unitContribution->formatPtBr()),
                new ToolCalculationSummaryItem('contribution_margin', 'Índice de margem de contribuição', $formattedMargin),
            ],
            details: ['input' => $input->toArray()],
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.0.0',
                steps: [
                    new CalculationMemoryStep('unit_contribution', 'Margem de contribuição unitária', 'preço de venda − custo variável unitário', ['sale_price' => $price, 'variable_cost' => $input->variableCost->minorAmount()], $contribution),
                    new CalculationMemoryStep('contribution_margin', 'Índice de margem de contribuição', 'margem unitária ÷ preço de venda', ['unit_contribution' => $contribution, 'sale_price' => $price], $formattedMargin, 'Percentual truncado em basis points para duas casas decimais.'),
                    new CalculationMemoryStep('break_even_units', 'Quantidade mínima', 'teto(custos fixos ÷ margem unitária)', ['fixed_costs' => $fixed, 'unit_contribution' => $contribution], $units, 'Arredondamento obrigatório para cima até a primeira unidade inteira.'),
                    new CalculationMemoryStep('break_even_revenue', 'Faturamento mínimo', 'quantidade mínima × preço de venda', ['break_even_units' => $units, 'sale_price' => $price], $revenue->minorAmount(), 'Multiplicação em centavos, sem ponto flutuante.'),
                ],
                assumptions: [
                    'Custos fixos, preço e custo variável devem representar o mesmo período e uma estrutura operacional comparável.',
                    'Preço e custo variável unitário permanecem constantes em todas as unidades consideradas.',
                    'Tributos, comissões e perdas variáveis devem estar incluídos no custo variável informado quando aplicáveis.',
                    'A quantidade mínima é arredondada para cima; por isso o faturamento exibido pode superar o ponto de equilíbrio teórico fracionário.',
                ],
                isEstimate: true,
            ),
        );
    }
}
