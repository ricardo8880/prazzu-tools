<?php

declare(strict_types=1);

namespace App\Tools\EcadRoyaltySimulator\Domain\Services;

use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\EcadRoyaltySimulator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    private const DECIMAL_SCALE = 10_000;

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada inválida para o simulador ECAD.');
        }
        if ($input->periods < 1 || $input->periods > 60) {
            throw new InvalidArgumentException('A projeção deve usar entre 1 e 60 períodos.');
        }

        $udaValue = Money::fromDecimal($input->udaValue);
        if ($udaValue->minorAmount() <= 0) {
            throw new InvalidArgumentException('O valor da UDA deve ser positivo.');
        }

        [$amount, $referenceLabel, $formula, $parameters] = match ($input->method) {
            'uda' => $this->byUda($udaValue, $input->udaQuantity),
            'uda_per_sqm' => $this->byArea($udaValue, $input->areaSquareMeters, $input->udaPerSquareMeter),
            'percentage' => $this->byPercentage($input->referenceAmount, $input->percentageRate),
            default => throw new InvalidArgumentException('Critério de cálculo não suportado.'),
        };

        $projected = $amount->multiply($input->periods);
        $memory = new CalculationMemory(
            '1.0.0',
            [
                new CalculationMemoryStep('reference', 'Critério informado', $formula, $parameters, $amount->minorAmount()),
                new CalculationMemoryStep('projection', 'Projeção por períodos', 'valor de referência × quantidade de períodos', ['periods' => $input->periods], $projected->minorAmount()),
            ],
            [],
            [
                'O enquadramento, a forma de utilização musical, o grau de utilização, a região socioeconômica e eventuais mínimos/descontos devem ser confirmados no Regulamento de Arrecadação e na Tabela de Preços vigentes do Ecad.',
                'A simulação não concede licença, não substitui boleto, orçamento ou autorização emitidos pelo Ecad.',
            ],
            true,
        );

        return new ToolCalculationResult(
            'simulador-ecad-direitos-autorais',
            '1.0.0',
            [
                new ToolCalculationSummaryItem('reference_amount', 'Valor de referência', $amount->formatPtBr()),
                new ToolCalculationSummaryItem('criterion', 'Critério utilizado', $referenceLabel),
                new ToolCalculationSummaryItem('projected_total', 'Total projetado', $projected->formatPtBr()),
            ],
            [
                'method' => $input->method,
                'uda_value' => $udaValue->formatPtBr(),
                'periods' => $input->periods,
                'official_2026_uda' => 'R$ 107,31 — vigente até dezembro de 2026',
                'official_source' => 'Ecad — Regulamento de Arrecadação, revisão 12/01/2026, Capítulo VII; UDA 2026 e critérios de precificação.',
            ],
            [
                new ToolCalculationWarning('orientation_only', 'Resultado orientativo: use os parâmetros da linha aplicável da tabela oficial e confirme o valor final diretamente com o Ecad antes da execução pública musical.', ToolCalculationWarningLevel::Info),
            ],
            calculationMemory: $memory,
        );
    }

    /** @return array{Money,string,string,array<string,mixed>} */
    private function byUda(Money $udaValue, ?string $quantity): array
    {
        $scaledQuantity = $this->parsePositiveDecimal($quantity, 'Quantidade de UDA');
        $amount = Money::fromMinor(IntegerRounding::divide($this->checkedMultiply($udaValue->minorAmount(), $scaledQuantity), self::DECIMAL_SCALE, RoundingMode::HalfUp));
        return [$amount, 'Quantidade de UDA', 'valor da UDA × quantidade de UDA', ['uda_value_minor' => $udaValue->minorAmount(), 'uda_quantity_scaled' => $scaledQuantity]];
    }

    /** @return array{Money,string,string,array<string,mixed>} */
    private function byArea(Money $udaValue, ?string $area, ?string $udaPerSquareMeter): array
    {
        $scaledArea = $this->parsePositiveDecimal($area, 'Área');
        $scaledRate = $this->parsePositiveDecimal($udaPerSquareMeter, 'UDA por m²');
        $udaQuantity = IntegerRounding::divide($this->checkedMultiply($scaledArea, $scaledRate), self::DECIMAL_SCALE, RoundingMode::HalfUp);
        $amount = Money::fromMinor(IntegerRounding::divide($this->checkedMultiply($udaValue->minorAmount(), $udaQuantity), self::DECIMAL_SCALE, RoundingMode::HalfUp));
        return [$amount, 'UDA por m²', 'área × UDA por m² × valor da UDA', ['area_scaled' => $scaledArea, 'uda_per_square_meter_scaled' => $scaledRate, 'uda_value_minor' => $udaValue->minorAmount()]];
    }

    /** @return array{Money,string,string,array<string,mixed>} */
    private function byPercentage(?string $referenceAmount, ?string $rate): array
    {
        if ($referenceAmount === null || $rate === null) {
            throw new InvalidArgumentException('Informe a base monetária e o percentual.');
        }
        $base = Money::fromDecimal($referenceAmount);
        if ($base->minorAmount() < 0) {
            throw new InvalidArgumentException('A base monetária não pode ser negativa.');
        }
        $percentage = Percentage::fromString($rate);
        if ($percentage->millionthsOfPercent() < 0 || $percentage->millionthsOfPercent() > 100_000_000) {
            throw new InvalidArgumentException('O percentual deve estar entre 0% e 100%.');
        }
        $amount = $base->percentage($percentage);
        return [$amount, 'Percentual sobre base informada', 'base monetária × percentual da tabela aplicável', ['base_minor' => $base->minorAmount(), 'percentage' => $percentage->toDecimalString()]];
    }

    private function parsePositiveDecimal(?string $value, string $label): int
    {
        if ($value === null) {
            throw new InvalidArgumentException("{$label} é obrigatório para este critério.");
        }
        $normalized = str_replace(',', '.', trim($value));
        if (! preg_match('/^(\d{1,9})(?:\.(\d{1,4}))?$/', $normalized, $matches)) {
            throw new InvalidArgumentException("{$label} inválida. Use até quatro casas decimais.");
        }
        $whole = (int) $matches[1];
        $fraction = (int) str_pad($matches[2] ?? '', 4, '0');
        $scaled = ($whole * self::DECIMAL_SCALE) + $fraction;
        if ($scaled <= 0) {
            throw new InvalidArgumentException("{$label} deve ser positiva.");
        }
        return $scaled;
    }
    private function checkedMultiply(int $left, int $right): int
    {
        if ($left !== 0 && $right !== 0 && abs($left) > intdiv(PHP_INT_MAX, abs($right))) {
            throw new InvalidArgumentException('Parâmetros fora do intervalo suportado.');
        }

        return $left * $right;
    }

}
