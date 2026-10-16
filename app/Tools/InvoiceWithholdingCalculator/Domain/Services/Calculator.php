<?php

declare(strict_types=1);
namespace App\Tools\InvoiceWithholdingCalculator\Domain\Services;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\InvoiceWithholdingCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;
final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if(!$input instanceof CalculationInput) throw new InvalidArgumentException('Incompatible backup-withholding input.');
        $gross=Money::fromDecimal($input->grossValue);
        $rate=Percentage::fromString($input->backupWithholdingRate);
        if($rate->millionthsOfPercent()<0 || $rate->millionthsOfPercent()>100_000_000) throw new InvalidArgumentException('Backup withholding rate must be between 0% and 100%.');
        $backup=$input->applyBackupWithholding ? $gross->percentage($rate) : Money::zero();
        $state=Money::fromDecimal($input->stateWithholding); $other=Money::fromDecimal($input->otherWithholding);
        $withheld=$backup->add($state)->add($other);
        if($withheld->minorAmount()>$gross->minorAmount()) throw new InvalidArgumentException('Total withholding cannot exceed gross payments.');
        $net=$gross->subtract($withheld);
        $memory=new CalculationMemory('2.0.0',[
            new CalculationMemoryStep('gross','Gross reportable payments','primary reportable payment',[], $gross->minorAmount()),
            new CalculationMemoryStep('backup','Federal backup withholding','gross reportable payments × backup withholding rate',['rate'=>$rate->toDecimalString()],$backup->minorAmount()),
            new CalculationMemoryStep('net','Net payment','gross payments − federal backup withholding − entered state/other withholding',[],$net->minorAmount()),
        ],assumptions:[
            'IRS backup withholding is currently 24% when it applies. This tool does not determine whether a payer is legally required to apply backup withholding.',
            'State withholding and other withholding are entered as dollar amounts because state and payment-type rules vary.',
        ],isEstimate:true);
        return new ToolCalculationResult('backup-withholding-calculator','2.0.0',[
            new ToolCalculationSummaryItem('gross','Gross reportable payments',$gross->formatEnUs()),
            new ToolCalculationSummaryItem('backup','Federal backup withholding',$backup->formatEnUs(),$input->applyBackupWithholding?$rate->toDecimalString().'%':'Not applied'),
            new ToolCalculationSummaryItem('other','State and other withholding',$state->add($other)->formatEnUs()),
            new ToolCalculationSummaryItem('net','Estimated net payment',$net->formatEnUs()),
        ],['input'=>$input->toArray(),'gross_minor'=>$gross->minorAmount(),'backup_withholding_minor'=>$backup->minorAmount(),'state_withholding_minor'=>$state->minorAmount(),'other_withholding_minor'=>$other->minorAmount(),'total_withheld_minor'=>$withheld->minorAmount(),'net_minor'=>$net->minorAmount()],[
            new ToolCalculationWarning('eligibility','Backup withholding applies only in specific IRS situations, such as certain TIN or underreporting cases. Confirm applicability before withholding.',ToolCalculationWarningLevel::Info),
        ],calculationMemory:$memory);
    }
}
