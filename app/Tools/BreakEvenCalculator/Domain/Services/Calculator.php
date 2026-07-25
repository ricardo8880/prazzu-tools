<?php

declare(strict_types=1);

namespace App\Tools\BreakEvenCalculator\Domain\Services;

use App\Core\Money\Money;
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
        $revenue = Money::fromMinor($units * $price);
        $marginBasisPoints = intdiv($contribution * 10000, $price);

        return new ToolCalculationResult(
            toolSlug: 'ponto-de-equilibrio',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('break_even_revenue', 'Faturamento mínimo', $revenue->formatPtBr(), 'Faturamento na primeira unidade inteira que cobre os custos.'),
                new ToolCalculationSummaryItem('break_even_units', 'Quantidade mínima', $units.' unidade'.($units === 1 ? '' : 's')),
                new ToolCalculationSummaryItem('unit_contribution', 'Margem de contribuição unitária', Money::fromMinor($contribution)->formatPtBr()),
                new ToolCalculationSummaryItem('contribution_margin', 'Índice de margem de contribuição', number_format($marginBasisPoints / 100, 2, ',', '.').' %'),
            ],
            details: [
                'input' => $input->toArray(),
                'memory' => [
                    'Margem unitária = preço - custo variável' => Money::fromMinor($contribution)->formatPtBr(),
                    'Unidades = arredondamento para cima de custos fixos ÷ margem unitária' => (string) $units,
                    'Faturamento mínimo = unidades × preço' => $revenue->formatPtBr(),
                ],
            ],
        );
    }
}
