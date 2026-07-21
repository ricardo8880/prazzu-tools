<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Unit;

use App\Tools\VacationCalculator\Application\Data\CalculationInput;
use App\Tools\VacationCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_returns_the_standardized_result_contract(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            monthlySalary: '3.000,00',
            acquisitionStartDate: '2025-01-01',
            vacationStartDate: '2026-07-01',
            convertOneThirdToCash: true,
        ));

        self::assertSame('calculadora-ferias', $result->toolSlug);
        self::assertSame('1.0.0', $result->schemaVersion);
        self::assertSame('entitled_days', $result->summary[0]->key);
        self::assertSame(400000, $result->details['remuneration']['gross_total_minor']);
        self::assertSame('2026-06-29', $result->details['periods']['payment_deadline']);
    }
}
