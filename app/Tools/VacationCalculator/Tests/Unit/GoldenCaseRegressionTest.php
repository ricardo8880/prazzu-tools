<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Core\Quality\Enums\GoldenCaseKind;
use App\Tools\VacationCalculator\Domain\Data\VacationInput;
use App\Tools\VacationCalculator\Domain\Services\VacationCalculator;
use App\Tools\VacationCalculator\Tests\Fixtures\GoldenCases;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class GoldenCaseRegressionTest extends TestCase
{
    public function test_approved_golden_cases_remain_stable(): void
    {
        foreach (GoldenCases::suite()->cases as $case) {
            if ($case->kind === GoldenCaseKind::InvalidInput) {
                try {
                    $this->input($case->input);
                    self::fail("O golden case {$case->identifier} deveria rejeitar a entrada.");
                } catch (InvalidValue) {
                    self::assertSame('InvalidValue', $case->expected['exception']);
                }

                continue;
            }

            $result = (new VacationCalculator)->calculate($this->input($case->input));

            foreach ($case->expected as $key => $expected) {
                $actual = match ($key) {
                    'entitled_days' => $result->entitledDays,
                    'leave_days' => $result->leaveDays,
                    'cash_allowance_days' => $result->cashAllowanceDays,
                    'gross_total_minor' => $result->grossTotal->minorAmount(),
                };
                self::assertSame($expected, $actual, "Falha no golden case {$case->identifier}: {$key}");
            }
        }
    }

    /** @param array<string, mixed> $input */
    private function input(array $input): VacationInput
    {
        return new VacationInput(
            monthlySalary: Money::fromMinor((int) $input['monthly_salary_minor']),
            acquisitionStartDate: new DateTimeImmutable('2025-01-01'),
            vacationStartDate: new DateTimeImmutable('2026-07-01'),
            unjustifiedAbsences: (int) $input['unjustified_absences'],
            convertOneThirdToCash: (bool) $input['cash_allowance'],
            commissionAverage: Money::zero(),
            overtimeAverage: Money::zero(),
            recurringAdditions: Money::zero(),
            otherDeductions: Money::zero(),
        );
    }
}
