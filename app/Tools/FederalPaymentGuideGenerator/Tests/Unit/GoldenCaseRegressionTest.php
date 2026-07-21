<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Quality\Enums\GoldenCaseKind;
use App\Tools\FederalPaymentGuideGenerator\Domain\Data\LatePaymentInput;
use App\Tools\FederalPaymentGuideGenerator\Domain\Services\GpsDueDateCalculator;
use App\Tools\FederalPaymentGuideGenerator\Domain\Services\LatePaymentCalculator;
use App\Tools\FederalPaymentGuideGenerator\Tests\Fixtures\GoldenCases;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GoldenCaseRegressionTest extends TestCase
{
    public function test_approved_golden_cases_remain_stable(): void
    {
        foreach (GoldenCases::suite()->cases as $case) {
            if (($case->input['operation'] ?? null) === 'gps_company_monthly') {
                $actual = (new GpsDueDateCalculator)->companyMonthly(new DateTimeImmutable($case->input['competence']))->format('Y-m-d');
                self::assertSame($case->expected['due_date'], $actual, $case->identifier);
                continue;
            }

            if ($case->kind === GoldenCaseKind::InvalidInput) {
                try {
                    $this->calculate($case->input);
                    self::fail('Era esperada entrada inválida em '.$case->identifier);
                } catch (InvalidArgumentException) {
                    self::addToAssertionCount(1);
                }
                continue;
            }

            $result = $this->calculate($case->input);
            $actual = [
                'days_late' => $result->calendarDaysLate,
                'penalty_percent' => $result->penaltyPercent,
                'penalty_minor' => $result->penalty->minorAmount(),
                'interest_minor' => $result->interest->minorAmount(),
                'total_minor' => $result->total->minorAmount(),
            ];

            foreach ($case->expected as $key => $expected) {
                self::assertSame($expected, $actual[$key], "Falha no golden case {$case->identifier}: {$key}");
            }
        }
    }

    /** @param array<string,mixed> $input */
    private function calculate(array $input): mixed
    {
        return (new LatePaymentCalculator)->calculate(new LatePaymentInput(
            Money::fromMinor($input['principal_minor']),
            new DateTimeImmutable($input['due_date']),
            new DateTimeImmutable($input['payment_date']),
            $input['selic'],
        ));
    }
}
