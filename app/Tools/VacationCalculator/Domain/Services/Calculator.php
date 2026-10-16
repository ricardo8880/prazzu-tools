<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Domain\Services;

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
use App\Tools\VacationCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) throw new InvalidArgumentException('Incompatible PTO input.');

        $accrued = $this->hours($input->accruedPtoHours);
        $planned = $this->hours($input->plannedPtoHours);
        $perPeriod = $this->hours($input->accrualHoursPerPayPeriod);
        $projected = $accrued + ($perPeriod * $input->payPeriodsBeforeLeave);
        if ($planned > $projected) throw new InvalidArgumentException('Planned PTO exceeds the projected PTO balance.');
        $remaining = $projected - $planned;
        $hourly = $input->hourlyMoney();
        $plannedValue = $this->hoursValue($hourly, $planned)->percentage($input->payoutRate());
        $remainingValue = $this->hoursValue($hourly, $remaining)->percentage($input->payoutRate());

        $memory = new CalculationMemory('2.0.0', [
            new CalculationMemoryStep('projected_balance', 'Projected PTO balance', 'current accrued hours + (accrual per pay period × pay periods before leave)', ['accrued_thousandths'=>$accrued,'per_period_thousandths'=>$perPeriod,'periods'=>$input->payPeriodsBeforeLeave], $projected),
            new CalculationMemoryStep('planned_value', 'Estimated paid-leave value', 'planned PTO hours × hourly rate × payout percentage', ['planned_thousandths'=>$planned,'payout_percent'=>$input->payoutPercent], $plannedValue->minorAmount()),
            new CalculationMemoryStep('remaining_balance', 'Remaining PTO balance', 'projected balance − planned PTO hours', [], $remaining),
        ], assumptions: [
            'Federal law generally does not require paid vacation or PTO; entitlement and payout depend on employer policy, agreement, and state/local law.',
            'The payout percentage is user supplied because PTO payout rules vary by employer and jurisdiction.',
        ], isEstimate: true);

        return new ToolCalculationResult('pto-calculator','2.0.0',[
            new ToolCalculationSummaryItem('projected_hours','Projected PTO balance',$this->formatHours($projected).' hours'),
            new ToolCalculationSummaryItem('planned_hours','Planned PTO',$this->formatHours($planned).' hours'),
            new ToolCalculationSummaryItem('remaining_hours','Remaining PTO',$this->formatHours($remaining).' hours'),
            new ToolCalculationSummaryItem('planned_value','Estimated value of planned PTO',$plannedValue->formatEnUs()),
            new ToolCalculationSummaryItem('remaining_value','Estimated value of remaining PTO',$remainingValue->formatEnUs()),
        ],[
            'input'=>$input->toArray(),'projected_hours_thousandths'=>$projected,'planned_hours_thousandths'=>$planned,'remaining_hours_thousandths'=>$remaining,'planned_value_minor'=>$plannedValue->minorAmount(),'remaining_value_minor'=>$remainingValue->minorAmount(),
        ],[new ToolCalculationWarning('policy','Check the employer PTO policy and applicable state law, especially for accrual caps and payout at termination.',ToolCalculationWarningLevel::Info)],calculationMemory:$memory);
    }

    private function hours(string $value): int
    {
        $v=str_replace(',','.',trim($value));
        if(!preg_match('/^\d+(?:\.\d{1,3})?$/',$v)) throw new InvalidArgumentException('Invalid PTO hours.');
        [$w,$f]=array_pad(explode('.',$v,2),2,'');
        return ((int)$w*1000)+(int)str_pad($f,3,'0');
    }
    private function hoursValue(Money $hourly,int $thousandths): Money { return Money::fromMinor(IntegerRounding::divide($hourly->minorAmount()*$thousandths,1000,RoundingMode::HalfUp)); }
    private function formatHours(int $thousandths): string { return rtrim(rtrim(number_format($thousandths/1000,3,'.',''),'0'),'.'); }
}
