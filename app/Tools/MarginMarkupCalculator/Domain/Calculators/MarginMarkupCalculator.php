<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Domain\Calculators;

use App\Core\Exceptions\InvalidValue;
use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Tools\MarginMarkupCalculator\Domain\Data\MarginMarkupResult;

final class MarginMarkupCalculator
{
    public const RULE_VERSION = '2.1.0';

    private const PERCENT_FACTOR = 100_000_000;

    public function calculate(
        Money $baseCost,
        Money $additionalCosts,
        Money $freightCost,
        Money $packagingCost,
        Money $fixedExpenses,
        Percentage $desiredMargin,
        Percentage $taxes,
        Percentage $commission,
        Percentage $cardFees,
        Percentage $marketplaceFees,
    ): MarginMarkupResult {
        $costs = [$baseCost, $additionalCosts, $freightCost, $packagingCost, $fixedExpenses];

        foreach ($costs as $cost) {
            if ($cost->minorAmount() < 0) {
                throw new InvalidValue('Os custos e as despesas não podem ser negativos.');
            }
        }

        $totalCost = array_reduce(
            $costs,
            static fn (Money $total, Money $cost): Money => $total->add($cost),
            Money::zero($baseCost->currency()),
        );

        if ($totalCost->minorAmount() === 0) {
            throw new InvalidValue('O custo total deve ser maior que zero.');
        }

        $percentageUnits = array_map(
            static fn (Percentage $percentage): int => $percentage->millionthsOfPercent(),
            [$desiredMargin, $taxes, $commission, $cardFees, $marketplaceFees],
        );

        foreach ($percentageUnits as $units) {
            if ($units < 0) {
                throw new InvalidValue('Margem, impostos, comissão e taxas não podem ser negativos.');
            }
        }

        $totalPercentageUnits = array_sum($percentageUnits);

        if ($totalPercentageUnits >= self::PERCENT_FACTOR) {
            throw new InvalidValue('A soma da margem, impostos, comissão e taxas deve ser menor que 100%.');
        }

        if ($totalCost->minorAmount() > intdiv(PHP_INT_MAX, self::PERCENT_FACTOR)) {
            throw new InvalidValue('O custo total informado é muito alto para o cálculo.');
        }

        $saleMinor = IntegerRounding::divide(
            $totalCost->minorAmount() * self::PERCENT_FACTOR,
            self::PERCENT_FACTOR - $totalPercentageUnits,
            RoundingMode::HalfUp,
        );
        $salePrice = Money::fromMinor($saleMinor, $totalCost->currency());

        $taxesAmount = $salePrice->percentage($taxes);
        $commissionAmount = $salePrice->percentage($commission);
        $cardFeesAmount = $salePrice->percentage($cardFees);
        $marketplaceFeesAmount = $salePrice->percentage($marketplaceFees);
        $grossProfit = $salePrice->subtract($totalCost);
        $netProfit = $grossProfit
            ->subtract($taxesAmount)
            ->subtract($commissionAmount)
            ->subtract($cardFeesAmount)
            ->subtract($marketplaceFeesAmount);

        $markupUnits = IntegerRounding::divide(
            $grossProfit->minorAmount() * self::PERCENT_FACTOR,
            $totalCost->minorAmount(),
            RoundingMode::HalfUp,
        );
        $multiplierScaled = IntegerRounding::divide(
            $salePrice->minorAmount() * 10_000,
            $totalCost->minorAmount(),
            RoundingMode::HalfUp,
        );

        return new MarginMarkupResult(
            totalCost: $totalCost,
            salePrice: $salePrice,
            grossProfit: $grossProfit,
            netProfit: $netProfit,
            taxesAmount: $taxesAmount,
            commissionAmount: $commissionAmount,
            cardFeesAmount: $cardFeesAmount,
            marketplaceFeesAmount: $marketplaceFeesAmount,
            margin: $desiredMargin,
            markup: Percentage::fromString($this->formatPercentageUnits($markupUnits)),
            markupMultiplier: $this->formatMultiplier($multiplierScaled),
            ruleVersion: self::RULE_VERSION,
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.0.0',
                steps: [
                    new CalculationMemoryStep(
                        key: 'total_cost',
                        label: 'Custo total do produto',
                        formula: 'custo base + custos adicionais + frete + embalagem + despesas fixas rateadas',
                        inputs: [
                            'base_cost_minor' => $baseCost->minorAmount(),
                            'additional_costs_minor' => $additionalCosts->minorAmount(),
                            'freight_cost_minor' => $freightCost->minorAmount(),
                            'packaging_cost_minor' => $packagingCost->minorAmount(),
                            'fixed_expenses_minor' => $fixedExpenses->minorAmount(),
                        ],
                        result: $totalCost->minorAmount(),
                    ),
                    new CalculationMemoryStep(
                        key: 'sale_price',
                        label: 'Preço de venda sugerido',
                        formula: 'custo total / (1 - margem líquida desejada - impostos - comissão - taxas)',
                        inputs: [
                            'total_cost_minor' => $totalCost->minorAmount(),
                            'desired_margin' => $desiredMargin->toDecimalString(),
                            'taxes' => $taxes->toDecimalString(),
                            'commission' => $commission->toDecimalString(),
                            'card_fees' => $cardFees->toDecimalString(),
                            'marketplace_fees' => $marketplaceFees->toDecimalString(),
                        ],
                        result: $salePrice->minorAmount(),
                        roundingPolicy: 'Divisão inteira com arredondamento HalfUp para centavos.',
                    ),
                    new CalculationMemoryStep(
                        key: 'net_profit',
                        label: 'Lucro líquido estimado',
                        formula: 'preço de venda - custo total - impostos - comissão - taxas de cartão - taxas de marketplace',
                        inputs: [
                            'sale_price_minor' => $salePrice->minorAmount(),
                            'total_cost_minor' => $totalCost->minorAmount(),
                            'taxes_minor' => $taxesAmount->minorAmount(),
                            'commission_minor' => $commissionAmount->minorAmount(),
                            'card_fees_minor' => $cardFeesAmount->minorAmount(),
                            'marketplace_fees_minor' => $marketplaceFeesAmount->minorAmount(),
                        ],
                        result: $netProfit->minorAmount(),
                    ),
                    new CalculationMemoryStep(
                        key: 'markup',
                        label: 'Markup sobre o custo',
                        formula: '(preço de venda - custo total) / custo total',
                        inputs: [
                            'gross_profit_minor' => $grossProfit->minorAmount(),
                            'total_cost_minor' => $totalCost->minorAmount(),
                        ],
                        result: $this->formatPercentageUnits($markupUnits),
                        roundingPolicy: 'Percentual arredondado HalfUp a seis casas decimais.',
                    ),
                ],
                assumptions: [
                    'A margem desejada é líquida sobre o preço de venda, depois das deduções percentuais informadas.',
                    'O markup exibido mede o acréscimo bruto sobre o custo; não é sinônimo de margem líquida.',
                    'Custos fixos devem ser previamente rateados por unidade ou venda pelo usuário.',
                    'Percentuais e custos são tratados como constantes no cenário calculado.',
                ],
                isEstimate: true,
            ),
        );
    }

    private function formatPercentageUnits(int $units): string
    {
        $whole = intdiv($units, 1_000_000);
        $fraction = str_pad((string) ($units % 1_000_000), 6, '0', STR_PAD_LEFT);

        return $whole.'.'.$fraction;
    }

    private function formatMultiplier(int $scaled): string
    {
        $whole = intdiv($scaled, 10_000);
        $fraction = rtrim(str_pad((string) ($scaled % 10_000), 4, '0', STR_PAD_LEFT), '0');

        return $fraction === '' ? (string) $whole : $whole.','.$fraction;
    }
}
