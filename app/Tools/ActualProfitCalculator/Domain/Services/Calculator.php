<?php

declare(strict_types=1);

namespace App\Tools\ActualProfitCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Tax\Normative\ActualProfitIncomeTaxRule;
use App\Core\Tools\Calculation\Data\{CalculationMemory,CalculationMemoryStep,ToolCalculationResult,ToolCalculationSummaryItem,ToolCalculationWarning};
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\{ToolCalculationInput,ToolCalculator};
use App\Tools\ActualProfitCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final readonly class Calculator implements ToolCalculator
{
 public function __construct(private ActualProfitIncomeTaxRule $rule) {}
 public function calculate(ToolCalculationInput $input): ToolCalculationResult {
  if(!$input instanceof CalculationInput) throw new InvalidArgumentException('Entrada inválida.');
  if($input->months<1||$input->months>12) throw new InvalidArgumentException('Período inválido.');
  $profit=Money::fromDecimal($input->accountingProfit); $add=Money::fromDecimal($input->additions); $exc=Money::fromDecimal($input->exclusions); $irLoss=Money::fromDecimal($input->irpjLossBalance); $csLoss=Money::fromDecimal($input->csllNegativeBalance);
  foreach([$add,$exc,$irLoss,$csLoss] as $m) if($m->minorAmount()<0) throw new InvalidArgumentException('Ajustes e saldos não podem ser negativos.');
  $before=Money::fromMinor(max(0,$profit->minorAmount()+$add->minorAmount()-$exc->minorAmount())); $limit=$before->percentage($this->rule->lossCompensationLimit());
  $irComp=Money::fromMinor(min($irLoss->minorAmount(),$limit->minorAmount())); $csComp=Money::fromMinor(min($csLoss->minorAmount(),$limit->minorAmount())); $irBase=$before->subtract($irComp); $csBase=$before->subtract($csComp);
  $irpj=$irBase->percentage($this->rule->irpjRate()); $threshold=$this->rule->additionalThresholdMinorPerMonth()*$input->months; $addBase=Money::fromMinor(max(0,$irBase->minorAmount()-$threshold)); $additional=$addBase->percentage($this->rule->irpjAdditionalRate()); $csll=$csBase->percentage($this->rule->csllRate()); $total=$irpj->add($additional)->add($csll);
  $memory=new CalculationMemory('1.0.0',[new CalculationMemoryStep('adjusted','Lucro ajustado','lucro contábil + adições − exclusões',[],$before->minorAmount()),new CalculationMemoryStep('irpj','IRPJ + adicional','15% + adicional de 10% sobre excedente',['months'=>$input->months],$irpj->add($additional)->minorAmount()),new CalculationMemoryStep('csll','CSLL','9% da base da CSLL',[],$csll->minorAmount())],[],['Estimativa assistida para pessoa jurídica em geral.','Ajustes e saldos devem vir dos controles fiscais do contribuinte.'],true);
  return new ToolCalculationResult('calculadora-lucro-real','1.0.0',[new ToolCalculationSummaryItem('adjusted_profit','Lucro ajustado',$before->formatPtBr()),new ToolCalculationSummaryItem('irpj','IRPJ + adicional',$irpj->add($additional)->formatPtBr()),new ToolCalculationSummaryItem('csll','CSLL',$csll->formatPtBr()),new ToolCalculationSummaryItem('total','IRPJ + CSLL',$total->formatPtBr())],['irpj_base'=>$irBase->formatPtBr(),'csll_base'=>$csBase->formatPtBr(),'irpj_compensation'=>$irComp->formatPtBr(),'csll_compensation'=>$csComp->formatPtBr(),'additional_base'=>$addBase->formatPtBr(),'months'=>$input->months],[new ToolCalculationWarning('assisted','Apuração assistida: não substitui ECD/ECF, e-Lalur/e-Lacs, incentivos, retenções ou revisão profissional.',ToolCalculationWarningLevel::Info)],calculationMemory:$memory);
 }
}
