<?php

declare(strict_types=1);
namespace App\Tools\LaborTerminationCalculator\Domain\Data;
use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use DateTimeImmutable;
final readonly class LaborTerminationResult
{
    /** @param list<string> $warnings */
    public function __construct(
        public DateTimeImmutable $terminationDate,
        public string $separationType,
        public Money $regularPayDue,
        public Money $ptoPayout,
        public Money $severancePay,
        public Money $bonusCommissionDue,
        public Money $otherPay,
        public Money $grossFinalPay,
        public Money $federalWithholding,
        public Money $stateLocalWithholding,
        public Money $otherDeductions,
        public Money $totalDeductions,
        public Money $netFinalPay,
        public array $warnings,
        public string $ruleVersion,
    ){}
    public function toArray():array
    {
        $m=fn(Money $v)=>$v->formatEnUs();
        return [
            'termination_date'=>$this->terminationDate->format('Y-m-d'),'separation_type'=>$this->separationType,
            'regular_pay_due'=>$m($this->regularPayDue),'pto_payout'=>$m($this->ptoPayout),'severance_pay'=>$m($this->severancePay),
            'bonus_commission_due'=>$m($this->bonusCommissionDue),'other_pay'=>$m($this->otherPay),'gross_final_pay'=>$m($this->grossFinalPay),
            'federal_withholding'=>$m($this->federalWithholding),'state_local_withholding'=>$m($this->stateLocalWithholding),'other_deductions'=>$m($this->otherDeductions),
            'total_deductions'=>$m($this->totalDeductions),'net_final_pay'=>$m($this->netFinalPay),'net_total'=>$m($this->netFinalPay),
            'warnings'=>$this->warnings,'rule_version'=>$this->ruleVersion,'calculation_memory'=>$this->calculationMemory()->toArray(),
        ];
    }
    public function calculationMemory():CalculationMemory
    {
        return new CalculationMemory($this->ruleVersion,[
            new CalculationMemoryStep('gross','Gross final pay','regular pay due + PTO payout + severance + bonus/commission + other pay',[],$this->grossFinalPay->minorAmount()),
            new CalculationMemoryStep('deductions','Entered withholding and deductions','federal + state/local + other deductions',[],$this->totalDeductions->minorAmount()),
            new CalculationMemoryStep('net','Estimated net final pay','gross final pay − entered deductions',[],$this->netFinalPay->minorAmount()),
        ],assumptions:$this->warnings,isEstimate:true);
    }
}
