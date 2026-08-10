<?php

declare(strict_types=1);

namespace App\Tools\TurnoverCalculator\Domain\Services;

use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\TurnoverCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public const SCHEMA_VERSION = '1.0.0';

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a calculadora de turnover.');
        }

        if ($input->admissions < 0 || $input->terminations < 0 || $input->averageHeadcount <= 0) {
            throw new InvalidArgumentException('Admissões e desligamentos não podem ser negativos, e o quadro médio deve ser maior que zero.');
        }

        // Mantém precisão determinística em basis points (1/100 de ponto percentual).
        $movementTimes100 = ($input->admissions + $input->terminations) * 50;
        $rateBasisPoints = intdiv(($movementTimes100 * 100) + intdiv($input->averageHeadcount, 2), $input->averageHeadcount);
        $rate = number_format($rateBasisPoints / 100, 2, ',', '.').'%';

        $memory = new CalculationMemory(
            schemaVersion: self::SCHEMA_VERSION,
            steps: [
                new CalculationMemoryStep(
                    'average_movement',
                    'Movimentação média',
                    '(admissões + desligamentos) ÷ 2',
                    ['admissions' => $input->admissions, 'terminations' => $input->terminations],
                    ($input->admissions + $input->terminations) / 2,
                    'Indicador operacional; não representa regra legal ou normativa.',
                ),
                new CalculationMemoryStep(
                    'turnover_rate',
                    'Taxa de turnover',
                    'movimentação média ÷ quadro médio × 100',
                    ['average_headcount' => $input->averageHeadcount],
                    $rate,
                    'Resultado arredondado para duas casas decimais.',
                ),
            ],
            assumptions: [
                'O quadro médio deve representar o mesmo período das admissões e desligamentos.',
                'A fórmula mede rotatividade geral e pode diferir de metodologias internas de RH.',
            ],
            isEstimate: false,
        );

        return new ToolCalculationResult(
            toolSlug: 'calculadora-turnover',
            schemaVersion: self::SCHEMA_VERSION,
            summary: [
                new ToolCalculationSummaryItem('turnover_rate', 'Taxa de turnover', $rate),
                new ToolCalculationSummaryItem('movements', 'Movimentações consideradas', $input->admissions + $input->terminations, 'Soma de admissões e desligamentos informados.'),
            ],
            details: ['input' => $input->toArray(), 'rate_basis_points' => $rateBasisPoints],
            calculationMemory: $memory,
        );
    }
}
