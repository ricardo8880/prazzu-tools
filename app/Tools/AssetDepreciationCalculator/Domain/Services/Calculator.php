<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Domain\Services;

use App\Core\Math\IntegerRounding;
use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\AssetDepreciationCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    private const METHODS = ['linear', 'declining_balance', 'sum_of_years_digits'];

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a calculadora de depreciação de ativos.');
        }

        if ($input->assets === []) {
            throw new InvalidArgumentException('Informe ao menos um ativo.');
        }

        $assets = [];
        $totalCost = Money::zero();
        $totalFirstYearDepreciation = Money::zero();
        $maxYears = 0;

        foreach ($input->assets as $index => $asset) {
            $name = trim($asset['name']);
            $cost = Money::fromDecimal($asset['value']);
            $years = $asset['useful_life_years'];
            $method = $asset['method'];

            if ($name === '' || $cost->minorAmount() <= 0 || $years < 1 || $years > 100 || ! in_array($method, self::METHODS, true)) {
                throw new InvalidArgumentException('Ativo inválido na posição '.($index + 1).'.');
            }

            $schedule = $this->schedule($cost, $years, $method);
            $firstYear = $schedule[0]['depreciation_minor'];
            $assets[] = [
                'name' => $name,
                'cost_minor' => $cost->minorAmount(),
                'useful_life_years' => $years,
                'method' => $method,
                'method_label' => $this->methodLabel($method),
                'first_year_depreciation_minor' => $firstYear,
                'first_year_monthly_minor' => IntegerRounding::divide($firstYear, 12),
                'first_year_book_value_minor' => $schedule[0]['book_value_minor'],
                'schedule' => $schedule,
            ];

            $totalCost = $totalCost->add($cost);
            $totalFirstYearDepreciation = $totalFirstYearDepreciation->add(Money::fromMinor($firstYear));
            $maxYears = max($maxYears, $years);
        }

        $portfolio = $this->portfolioProjection($assets, $maxYears);
        $main = $assets[0];
        $mainCost = Money::fromMinor($main['cost_minor']);
        $mainAnnual = Money::fromMinor($main['first_year_depreciation_minor']);
        $mainMonthly = Money::fromMinor($main['first_year_monthly_minor']);
        $mainBookValue = Money::fromMinor($main['first_year_book_value_minor']);

        return new ToolCalculationResult(
            toolSlug: 'calculadora-depreciacao-ativos',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('asset_value', 'Valor do bem', $mainCost->formatPtBr()),
                new ToolCalculationSummaryItem('monthly_depreciation', $main['method'] === 'linear' ? 'Depreciação mensal' : 'Média mensal no 1º ano', $mainMonthly->formatPtBr()),
                new ToolCalculationSummaryItem('annual_depreciation', $main['method'] === 'linear' ? 'Depreciação anual' : 'Depreciação no 1º ano', $mainAnnual->formatPtBr()),
                new ToolCalculationSummaryItem('book_value_after_year_one', 'Valor contábil após 1 ano', $mainBookValue->formatPtBr()),
            ],
            details: [
                'assets' => $assets,
                'portfolio' => $portfolio,
                'portfolio_cost_minor' => $totalCost->minorAmount(),
                'portfolio_first_year_depreciation_minor' => $totalFirstYearDepreciation->minorAmount(),
                'portfolio_first_year_book_value_minor' => $totalCost->subtract($totalFirstYearDepreciation)->minorAmount(),
            ],
            warnings: [
                new ToolCalculationWarning(
                    'scope',
                    'A vida útil e o método devem refletir a política contábil aplicável ao ativo. A ferramenta não define enquadramento fiscal, valor residual nem taxa normativa automaticamente.',
                    ToolCalculationWarningLevel::Info,
                ),
                new ToolCalculationWarning(
                    'residual_value',
                    'Esta versão considera valor residual igual a zero. Se houver valor residual material, ajuste a base depreciável antes de usar o cálculo.',
                    ToolCalculationWarningLevel::Info,
                ),
            ],
            calculationMemory: new CalculationMemory('1.0.0', [
                new CalculationMemoryStep(
                    'depreciable_base',
                    'Base depreciável',
                    'valor do ativo − valor residual; nesta versão, valor residual = 0',
                    ['asset_value_minor' => $main['cost_minor'], 'residual_value_minor' => 0],
                    $main['cost_minor'],
                    'Valores monetários são calculados em centavos, sem ponto flutuante.',
                ),
                new CalculationMemoryStep(
                    'first_year_depreciation',
                    'Depreciação do primeiro ano',
                    $this->formula($main['method']),
                    ['useful_life_years' => $main['useful_life_years'], 'method' => $main['method']],
                    $main['first_year_depreciation_minor'],
                    'Ajustes de centavos são absorvidos na projeção para que o valor contábil nunca fique negativo e termine em zero.',
                ),
                new CalculationMemoryStep(
                    'first_year_book_value',
                    'Valor contábil ao fim do primeiro ano',
                    'valor contábil inicial − depreciação acumulada',
                    ['opening_book_value_minor' => $main['cost_minor'], 'depreciation_minor' => $main['first_year_depreciation_minor']],
                    $main['first_year_book_value_minor'],
                ),
            ]),
        );
    }

    /** @return list<array{year:int,opening_book_value_minor:int,depreciation_minor:int,accumulated_depreciation_minor:int,book_value_minor:int}> */
    private function schedule(Money $cost, int $years, string $method): array
    {
        $costMinor = $cost->minorAmount();
        $schedule = [];
        $opening = $costMinor;
        $accumulated = 0;
        $sumDigits = IntegerRounding::divide($years * ($years + 1), 2);

        for ($year = 1; $year <= $years; $year++) {
            $remaining = $costMinor - $accumulated;

            $depreciation = match ($method) {
                'linear' => $this->linearYear($costMinor, $years, $year, $accumulated),
                'declining_balance' => $year === $years
                    ? $remaining
                    : min($remaining, IntegerRounding::divide($opening * 2, $years)),
                'sum_of_years_digits' => $year === $years
                    ? $remaining
                    : min($remaining, IntegerRounding::divide($costMinor * ($years - $year + 1), $sumDigits)),
                default => throw new InvalidArgumentException('Método de depreciação inválido.'),
            };

            $depreciation = max(0, min($remaining, $depreciation));
            $accumulated += $depreciation;
            $bookValue = max(0, $costMinor - $accumulated);

            $schedule[] = [
                'year' => $year,
                'opening_book_value_minor' => $opening,
                'depreciation_minor' => $depreciation,
                'accumulated_depreciation_minor' => $accumulated,
                'book_value_minor' => $bookValue,
            ];
            $opening = $bookValue;
        }

        return $schedule;
    }

    private function linearYear(int $costMinor, int $years, int $year, int $accumulated): int
    {
        if ($year === $years) {
            return $costMinor - $accumulated;
        }

        $targetAccumulated = IntegerRounding::divide($costMinor * $year, $years);

        return $targetAccumulated - $accumulated;
    }

    /** @param list<array<string,mixed>> $assets @return list<array{year:int,depreciation_minor:int,accumulated_depreciation_minor:int,book_value_minor:int}> */
    private function portfolioProjection(array $assets, int $maxYears): array
    {
        $projection = [];
        $accumulated = 0;

        for ($year = 1; $year <= $maxYears; $year++) {
            $depreciation = 0;
            $bookValue = 0;
            foreach ($assets as $asset) {
                $row = $asset['schedule'][$year - 1] ?? null;
                if ($row !== null) {
                    $depreciation += $row['depreciation_minor'];
                    $bookValue += $row['book_value_minor'];
                }
            }
            $accumulated += $depreciation;
            $projection[] = [
                'year' => $year,
                'depreciation_minor' => $depreciation,
                'accumulated_depreciation_minor' => $accumulated,
                'book_value_minor' => $bookValue,
            ];
        }

        return $projection;
    }

    private function methodLabel(string $method): string
    {
        return match ($method) {
            'linear' => 'Linear',
            'declining_balance' => 'Saldos decrescentes (duplo)',
            'sum_of_years_digits' => 'Soma dos dígitos dos anos',
            default => 'Desconhecido',
        };
    }

    private function formula(string $method): string
    {
        return match ($method) {
            'linear' => 'base depreciável ÷ vida útil em anos',
            'declining_balance' => 'valor contábil inicial do ano × (2 ÷ vida útil), limitado ao saldo remanescente',
            'sum_of_years_digits' => 'base depreciável × anos restantes ÷ soma dos dígitos da vida útil',
            default => '',
        };
    }
}
