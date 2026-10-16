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
    public const RULE_VERSION='2026-US-1.0';
    public function calculate(DateTimeImmutable $terminationDate,string $separationType,Money $hourlyRate,float $regularHoursDue,float $unusedPtoHours,Percentage $ptoPayoutRate,Money $weeklyBasePay,float $severanceWeeks,Money $bonusCommissionDue,Money $otherPay,Money $federalWithholding,Money $stateLocalWithholding,Money $otherDeductions):LaborTerminationResult
    {
        $regular=$this->hoursValue($hourlyRate,$regularHoursDue);
        $pto=$this->hoursValue($hourlyRate,$unusedPtoHours)->percentage($ptoPayoutRate);
        $severance=Money::fromMinor(IntegerRounding::divide((int)round($weeklyBasePay->minorAmount()*$severanceWeeks*1000),1000,RoundingMode::HalfUp));
        $gross=$regular->add($pto)->add($severance)->add($bonusCommissionDue)->add($otherPay);
        $ded=$federalWithholding->add($stateLocalWithholding)->add($otherDeductions);
        if($ded->minorAmount()>$gross->minorAmount()) throw new InvalidArgumentException('Deductions cannot exceed gross final pay.');
        $net=$gross->subtract($ded);
        $warnings=[
            'Federal law does not generally require severance pay; severance usually depends on an agreement, plan, policy, or applicable law.',
            'The FLSA does not establish one nationwide deadline for final wages; state law can impose specific timing requirements.',
            'Unused PTO payout rules vary by state and employer policy. Enter the payout percentage that applies to the employee.',
            'Tax withholding on final wages and severance can differ from this estimate. Enter actual withholding amounts when known.',
        ];
        return new LaborTerminationResult($terminationDate,$separationType,$regular,$pto,$severance,$bonusCommissionDue,$otherPay,$gross,$federalWithholding,$stateLocalWithholding,$otherDeductions,$ded,$net,$warnings,self::RULE_VERSION);
    }
    private function hoursValue(Money $rate,float $hours):Money{return Money::fromMinor((int)round($rate->minorAmount()*$hours));}
}
