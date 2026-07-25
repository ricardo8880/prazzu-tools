<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Application\Actions;

use App\Core\Money\Money;

final readonly class CalculateEmployeeBatch
{
    public function __construct(private CalculateTool $calculator) {}

    /** @param array<string, mixed> $data */
    public function execute(array $data): array
    {
        $rows = [];
        $departmentTotals = [];
        $monthlyTotal = Money::zero();
        $annualTotal = Money::zero();

        foreach ($data['employees'] as $employee) {
            $result = $this->calculator->execute($employee);
            $amounts = $result->details['amounts'];
            $department = trim((string) ($employee['department'] ?? '')) ?: 'Sem departamento';
            $monthly = (int) $amounts['monthly_cost_minor'];
            $annual = (int) $amounts['annual_cost_minor'];
            $monthlyMoney = Money::fromMinor($monthly);
            $annualMoney = Money::fromMinor($annual);
            $monthlyTotal = $monthlyTotal->add($monthlyMoney);
            $annualTotal = $annualTotal->add($annualMoney);
            $departmentTotals[$department] = ($departmentTotals[$department] ?? Money::zero())->add($monthlyMoney);

            $rows[] = [
                'name' => trim((string) ($employee['employee_name'] ?? '')) ?: 'Funcionário sem nome',
                'department' => $department,
                'role' => trim((string) ($employee['role'] ?? '')),
                'input' => $employee,
                'result' => $result->toArray(),
                'amounts' => $amounts,
                'monthly_cost' => Money::fromMinor($monthly)->formatPtBr(),
                'annual_cost' => Money::fromMinor($annual)->formatPtBr(),
                'hourly_cost' => Money::fromMinor((int) $amounts['hourly_cost_minor'])->formatPtBr(),
            ];
        }

        $departments = [];
        foreach ($departmentTotals as $department => $total) {
            $departmentAnnual = $total->multiply(12);
            $departments[] = [
                'department' => $department,
                'monthly_cost_minor' => $total->minorAmount(),
                'monthly_cost' => $total->formatPtBr(),
                'annual_cost_minor' => $departmentAnnual->minorAmount(),
                'annual_cost' => $departmentAnnual->formatPtBr(),
            ];
        }

        $projectionStart = new \DateTimeImmutable('first day of this month');

        return [
            'scenario_name' => trim((string) ($data['scenario_name'] ?? '')) ?: 'Cenário em lote',
            'employees' => $rows,
            'departments' => $departments,
            'employee_count' => count($rows),
            'monthly_total_minor' => $monthlyTotal->minorAmount(),
            'monthly_total' => $monthlyTotal->formatPtBr(),
            'annual_total_minor' => $annualTotal->minorAmount(),
            'annual_total' => $annualTotal->formatPtBr(),
            'projection_assumption' => 'Projeção-base sem reajustes, admissões, desligamentos ou alteração de benefícios.',
            'projection' => array_map(
                static fn (int $offset): array => [
                    'competence' => $projectionStart->modify("+{$offset} months")->format('m/Y'),
                    'cost_minor' => $monthlyTotal->minorAmount(),
                    'cost' => $monthlyTotal->formatPtBr(),
                ],
                range(0, 11),
            ),
        ];
    }
}
