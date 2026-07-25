<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Tools\Calculation;

use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use PHPUnit\Framework\TestCase;

final class CalculationMemoryTest extends TestCase
{
    public function test_it_serializes_reproducible_calculation_memory_in_the_standard_result(): void
    {
        $memory = new CalculationMemory(
            schemaVersion: '1.0.0',
            steps: [new CalculationMemoryStep(
                key: 'employee_cost',
                label: 'Custo total',
                formula: 'salary + charges',
                inputs: ['salary_minor' => 100000, 'charges_minor' => 40000],
                result: 140000,
                roundingPolicy: 'integer_minor_units_half_up',
            )],
            assumptions: ['Valores informados pelo usuário.'],
            isEstimate: true,
        );

        $result = new ToolCalculationResult(
            toolSlug: 'example',
            schemaVersion: '1.0.0',
            summary: [new ToolCalculationSummaryItem('total', 'Total', 140000)],
            calculationMemory: $memory,
        );

        self::assertSame('1.0.0', $result->toArray()['calculation_memory']['schema_version']);
        self::assertTrue($result->toPersistenceArray()['calculation_memory']['is_estimate']);
    }
}
