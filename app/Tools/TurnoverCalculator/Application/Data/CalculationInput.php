<?php

declare(strict_types=1);

namespace App\Tools\TurnoverCalculator\Application\Data;

use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public int $admissions,
        public int $terminations,
        public int $averageHeadcount,
    ) {}

    public function toArray(): array
    {
        return [
            'admissions' => $this->admissions,
            'terminations' => $this->terminations,
            'average_headcount' => $this->averageHeadcount,
        ];
    }
}
