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
            throw new InvalidArgumentException('Incompatible turnover input.');
        }

        if ($input->admissions < 0 || $input->terminations < 0 || $input->averageHeadcount <= 0) {
            throw new InvalidArgumentException('Hires and separations cannot be negative, and average headcount must be greater than zero.');
        }

        // Keeps deterministic precision with integer arithmetic: the average may end in .5 and the rate uses basis points.
        $movementTotal = $input->admissions + $input->terminations;
        $averageMovement = intdiv($movementTotal, 2).($movementTotal % 2 === 0 ? '' : '.5');
        $movementTimes100 = $movementTotal * 50;
        $rateBasisPoints = intdiv(($movementTimes100 * 100) + intdiv($input->averageHeadcount, 2), $input->averageHeadcount);
        $rate = number_format(intdiv($rateBasisPoints, 100), 0, '.', ',').'.'.str_pad((string) ($rateBasisPoints % 100), 2, '0', STR_PAD_LEFT).'%';

        $memory = new CalculationMemory(
            schemaVersion: self::SCHEMA_VERSION,
            steps: [
                new CalculationMemoryStep(
                    'average_movement',
                    'Average workforce movement',
                    '(hires + separations) ÷ 2',
                    ['admissions' => $input->admissions, 'terminations' => $input->terminations],
                    $averageMovement,
                    'Operational metric; not a legal or regulatory rule.',
                ),
                new CalculationMemoryStep(
                    'turnover_rate',
                    'Turnover rate',
                    'average workforce movement ÷ average headcount × 100',
                    ['average_headcount' => $input->averageHeadcount],
                    $rate,
                    'Result rounded to two decimal places.',
                ),
            ],
            assumptions: [
                'Average headcount should cover the same period as hires and separations.',
                'This formula measures general turnover and may differ from internal HR methodologies.',
            ],
            isEstimate: false,
        );

        return new ToolCalculationResult(
            toolSlug: 'employee-turnover-calculator',
            schemaVersion: self::SCHEMA_VERSION,
            summary: [
                new ToolCalculationSummaryItem('turnover_rate', 'Turnover rate', $rate),
                new ToolCalculationSummaryItem('movements', 'Employee movements', $input->admissions + $input->terminations, 'Sum of entered hires and separations.'),
            ],
            details: ['input' => $input->toArray(), 'rate_basis_points' => $rateBasisPoints],
            calculationMemory: $memory,
        );
    }
}
