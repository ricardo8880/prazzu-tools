<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Application\Actions;

use App\Core\Money\Money;

final readonly class CompareCostScenarios
{
    public function __construct(private CalculateEmployeeBatch $batch) {}

    /** @param array<string, mixed> $data */
    public function execute(array $data): array
    {
        $scenarios = [];
        $lowest = null;

        foreach ($data['scenarios'] as $scenario) {
            $result = $this->batch->execute($scenario);
            $scenarios[] = $result;

            if ($lowest === null || $result['annual_total_minor'] < $lowest['annual_total_minor']) {
                $lowest = $result;
            }
        }

        foreach ($scenarios as &$scenario) {
            $difference = $scenario['annual_total_minor'] - (int) $lowest['annual_total_minor'];
            $scenario['difference_from_lowest_minor'] = $difference;
            $scenario['difference_from_lowest'] = Money::fromMinor($difference)->formatPtBr();
        }
        unset($scenario);

        return [
            'scenarios' => $scenarios,
            'lowest_scenario' => $lowest['scenario_name'],
            'lowest_annual_cost' => $lowest['annual_total'],
        ];
    }
}
