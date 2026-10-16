<?php

declare(strict_types=1);
namespace App\Tools\TaxReformSimulator\Domain\Services;
use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
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
use App\Tools\TaxReformSimulator\Application\Data\CalculationInput;
use InvalidArgumentException;
final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if(!$input instanceof CalculationInput) throw new InvalidArgumentException('Incompatible sales-tax input.');
        if($input->quantity<1 || $input->quantity>1_000_000) throw new InvalidArgumentException('Quantity must be between 1 and 1,000,000.');
        $unit=Money::fromDecimal($input->price); $entered=$unit->multiply($input->quantity);
        $state=Percentage::fromString($input->stateRate); $local=Percentage::fromString($input->localRate); $district=Percentage::fromString($input->districtRate);
        $combinedMillionths=$state->millionthsOfPercent()+$local->millionthsOfPercent()+$district->millionthsOfPercent();
        if($combinedMillionths<0 || $combinedMillionths>100_000_000) throw new InvalidArgumentException('Combined sales-tax rate must be between 0% and 100%.');
        $combined=Percentage::fromString($this->percentString($combinedMillionths));
        if($input->taxIncluded){
            $den=100_000_000+$combinedMillionths;
            $subtotal=Money::fromMinor(IntegerRounding::divide($entered->minorAmount()*100_000_000,$den,RoundingMode::HalfUp));
            $tax=$entered->subtract($subtotal); $total=$entered;
        } else { $subtotal=$entered; $tax=$subtotal->percentage($combined); $total=$subtotal->add($tax); }
        $memory=new CalculationMemory('2.0.0',[
            new CalculationMemoryStep('combined_rate','Combined sales-tax rate','state rate + local rate + district/special rate',['state'=>$input->stateRate,'local'=>$input->localRate,'district'=>$input->districtRate],$combined->toDecimalString().'%'),
            new CalculationMemoryStep('tax','Estimated sales tax',$input->taxIncluded?'extract tax from tax-inclusive total':'taxable subtotal × combined rate',['subtotal_minor'=>$subtotal->minorAmount()],$tax->minorAmount()),
        ],assumptions:[
            'The United States has no general federal sales tax; state and local rates and taxability rules vary.',
            'The user must enter the correct rates for the transaction location and confirm whether the item or service is taxable.',
            'Marketplace, nexus, sourcing, exemptions, tax holidays, and product-specific rules are not determined automatically.',
        ],isEstimate:true);
        return new ToolCalculationResult('sales-tax-calculator','2.0.0',[
            new ToolCalculationSummaryItem('subtotal','Taxable subtotal',$subtotal->formatEnUs()),
            new ToolCalculationSummaryItem('combined_rate','Combined rate',$combined->toDecimalString().'%'),
            new ToolCalculationSummaryItem('tax','Estimated sales tax',$tax->formatEnUs()),
            new ToolCalculationSummaryItem('total','Total including tax',$total->formatEnUs()),
        ],['input'=>$input->toArray(),'subtotal_minor'=>$subtotal->minorAmount(),'tax_minor'=>$tax->minorAmount(),'total_minor'=>$total->minorAmount(),'combined_rate'=>$combined->toDecimalString()],[
            new ToolCalculationWarning('jurisdiction','Confirm the exact state, county, city, district, sourcing, and product taxability rules before charging or remitting tax.',ToolCalculationWarningLevel::Info),
        ],calculationMemory:$memory);
    }
    private function percentString(int $millionths): string { $w=intdiv($millionths,1_000_000); $f=str_pad((string)($millionths%1_000_000),6,'0',STR_PAD_LEFT); return rtrim(rtrim($w.'.'.$f,'0'),'.'); }
}
