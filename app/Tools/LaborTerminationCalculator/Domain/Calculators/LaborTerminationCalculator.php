<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Domain\Calculators;

use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\LaborTerminationCalculator\Domain\Data\LaborTerminationResult;
use DateTimeImmutable;
use InvalidArgumentException;

final class LaborTerminationCalculator
{
    public const RULE_VERSION = '2026-US-1.1';

    public function calculate(
        DateTimeImmutable $terminationDate,
        string $separationType,
        Money $hourlyRate,
        string $regularHoursDue,
        string $unusedPtoHours,
        Percentage $ptoPayoutRate,
        Money $weeklyBasePay,
        string $severanceWeeks,
        Money $bonusCommissionDue,
        Money $otherPay,
        Money $federalWithholding,
        Money $stateLocalWithholding,
        Money $otherDeductions,
    ): LaborTerminationResult {
        $regular = $this->hoursValue($hourlyRate, $this->decimalThousandths($regularHoursDue, 'regular hours'));
        $pto = $this->hoursValue($hourlyRate, $this->decimalThousandths($unusedPtoHours, 'unused PTO hours'))->percentage($ptoPayoutRate);
        $severance = Money::fromMinor(IntegerRounding::divide(
            $weeklyBasePay->minorAmount() * $this->decimalThousandths($severanceWeeks, 'severance weeks'),
            1_000,
            RoundingMode::HalfUp,
        ));

        $gross = $regular->add($pto)->add($severance)->add($bonusCommissionDue)->add($otherPay);
        $deductions = $federalWithholding->add($stateLocalWithholding)->add($otherDeductions);
        if ($deductions->minorAmount() > $gross->minorAmount()) {
            throw new InvalidArgumentException('Deductions cannot exceed gross final pay.');
        }

        $net = $gross->subtract($deductions);
        $warnings = [
            'Federal law does not generally require severance pay; severance usually depends on an agreement, plan, policy, or applicable law.',
            'Final-pay timing requirements vary by state and the reason for separation.',
            'Unused PTO payout rules vary by state and employer policy. Enter the payout percentage that applies to the employee.',
            'Tax withholding on final wages and severance can differ from this estimate. Enter actual withholding amounts when known.',
        ];

        return new LaborTerminationResult($terminationDate, $separationType, $regular, $pto, $severance, $bonusCommissionDue, $otherPay, $gross, $federalWithholding, $stateLocalWithholding, $otherDeductions, $deductions, $net, $warnings, self::RULE_VERSION);
    }

    private function hoursValue(Money $rate, int $hoursThousandths): Money
    {
        return Money::fromMinor(IntegerRounding::divide($rate->minorAmount() * $hoursThousandths, 1_000, RoundingMode::HalfUp));
    }

    private function decimalThousandths(string $value, string $label): int
    {
        $normalized = trim(str_replace(',', '.', $value));
        if (! preg_match('/^\d+(?:\.\d{1,3})?$/', $normalized)) {
            throw new InvalidArgumentException("Invalid {$label} value.");
        }

        [$whole, $fraction] = array_pad(explode('.', $normalized, 2), 2, '');

        return ((int) $whole * 1_000) + (int) str_pad($fraction, 3, '0');
    }
}
