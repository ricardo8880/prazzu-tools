<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Domain\Services;

use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\OvertimeCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Incompatible overtime input.');
        }

        $regularHours = $input->regularThousandths();
        $overtimeHours = $input->overtimeThousandths();
        if ($regularHours > 40_000) {
            throw new InvalidArgumentException('Regular hours cannot exceed 40 in this weekly FLSA estimator. Put hours over 40 in overtime hours.');
        }
        if ($regularHours + $overtimeHours > 168_000) {
            throw new InvalidArgumentException('Weekly hours cannot exceed 168.');
        }

        $hourly = $input->hourlyMoney();
        $bonus = $input->bonusMoney();
        $totalHours = $regularHours + $overtimeHours;

        // For this hourly-worker estimator, straight-time base wages are paid for every
        // hour worked. A nondiscretionary bonus is allocated over all hours worked and
        // increases only the overtime premium portion; adding the full bonus again to a
        // 1.5x regular-rate overtime amount would double-count the bonus allocation.
        $bonusPerHour = $totalHours > 0
            ? Money::fromMinor(IntegerRounding::divide($bonus->minorAmount() * 1_000, $totalHours, RoundingMode::HalfUp))
            : Money::zero();
        $regularRate = $hourly->add($bonusPerHour);

        $regularPay = $this->hoursValue($hourly, $regularHours);
        $overtimeStraightTime = $this->hoursValue($hourly, $overtimeHours);
        $overtimePremium = $this->hoursValue($regularRate->percentage($input->premiumPercent()), $overtimeHours);
        $overtimePay = $overtimeStraightTime->add($overtimePremium);
        $weeklyTotal = $regularPay->add($overtimePay)->add($bonus);

        $memory = new CalculationMemory('2.1.0', [
            new CalculationMemoryStep('regular_rate', 'Regular rate', 'hourly rate + allocated nondiscretionary bonus per hour', ['hours_thousandths' => $totalHours, 'bonus_minor' => $bonus->minorAmount()], $regularRate->minorAmount()),
            new CalculationMemoryStep('regular_pay', 'Regular-hours pay', 'hourly rate × regular hours', ['regular_hours_thousandths' => $regularHours], $regularPay->minorAmount()),
            new CalculationMemoryStep('overtime_straight_time', 'Overtime straight-time pay', 'hourly rate × overtime hours', ['overtime_hours_thousandths' => $overtimeHours], $overtimeStraightTime->minorAmount()),
            new CalculationMemoryStep('overtime_premium', 'Overtime premium', 'regular rate × (overtime multiplier − 1.0) × overtime hours', ['overtime_hours_thousandths' => $overtimeHours, 'multiplier' => $input->overtimeMultiplier], $overtimePremium->minorAmount()),
            new CalculationMemoryStep('weekly_total', 'Estimated weekly pay', 'regular-hours pay + overtime straight time + overtime premium + nondiscretionary bonus', [], $weeklyTotal->minorAmount()),
        ], assumptions: [
            'This estimator is for a covered, nonexempt hourly employee under the federal FLSA.',
            'Federal overtime is generally at least 1.5× the regular rate after 40 hours in a workweek.',
            'Nondiscretionary bonuses generally must be included in the regular rate unless a statutory exclusion applies.',
            'State law, collective bargaining agreements, contracts, or employer policy can require more favorable rules.',
            'Federal law does not generally require a night, weekend, or holiday premium by itself.',
        ], isEstimate: true);

        return new ToolCalculationResult('overtime-pay-calculator', '2.1.0', [
            new ToolCalculationSummaryItem('regular_rate', 'Regular rate used', $regularRate->formatEnUs()),
            new ToolCalculationSummaryItem('regular_pay', 'Regular-hours pay', $regularPay->formatEnUs()),
            new ToolCalculationSummaryItem('overtime_pay', 'Overtime pay', $overtimePay->formatEnUs()),
            new ToolCalculationSummaryItem('bonus', 'Nondiscretionary bonus', $bonus->formatEnUs()),
            new ToolCalculationSummaryItem('weekly_total', 'Estimated weekly total', $weeklyTotal->formatEnUs()),
        ], [
            'input' => $input->toArray(),
            'hourly_minor' => $hourly->minorAmount(),
            'regular_rate_minor' => $regularRate->minorAmount(),
            'regular_pay_minor' => $regularPay->minorAmount(),
            'overtime_straight_time_minor' => $overtimeStraightTime->minorAmount(),
            'overtime_premium_minor' => $overtimePremium->minorAmount(),
            'overtime_minor' => $overtimePay->minorAmount(),
            'total_minor' => $weeklyTotal->minorAmount(),
        ], [
            new ToolCalculationWarning('state_law', 'Check state overtime rules and any applicable agreement before relying on the result.', ToolCalculationWarningLevel::Info),
        ], calculationMemory: $memory);
    }

    private function hoursValue(Money $hourly, int $thousandths): Money
    {
        return Money::fromMinor(IntegerRounding::divide($hourly->minorAmount() * $thousandths, 1_000, RoundingMode::HalfUp));
    }
}
