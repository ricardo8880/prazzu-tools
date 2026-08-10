<?php

declare(strict_types=1);

namespace App\Tools\IcmsStCalculator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\IcmsStCalculator\Application\Data\CalculationInput;
use App\Tools\IcmsStCalculator\Domain\Rules\IcmsStRule;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function __construct(private readonly IcmsStRule $rule = new IcmsStRule()) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) throw new InvalidArgumentException('Entrada incompatível com a calculadora de ICMS-ST.');
        $referenceDate = ReferenceDate::fromString($input->competence.'-01');
        if (! $this->rule->effectivePeriod()->contains($referenceDate)) throw new InvalidArgumentException('Esta versão normativa atende competências de 2026.');
        if (! in_array($input->operationType,['internal','interstate'],true)) throw new InvalidArgumentException('Tipo de operação inválido.');

        $internalRate = Percentage::fromString($input->internalRate);
        $interstateRate = Percentage::fromString($input->operationType === 'interstate' ? $input->interstateRate : $input->internalRate);
        $fcpRate = Percentage::fromString($input->fcpRate === '' ? '0' : $input->fcpRate);
        $originalMva = Percentage::fromString($input->originalMva);
        $usedMva = $input->operationType === 'interstate' && $input->adjustMva
            ? $this->adjustedMva($originalMva,$interstateRate,$internalRate)
            : $originalMva;

        $components = [Money::fromDecimal($input->merchandiseValue),Money::fromDecimal($input->freight),Money::fromDecimal($input->insurance),Money::fromDecimal($input->otherCharges),Money::fromDecimal($input->ipi)];
        $operationBase = Money::zero(); foreach($components as $component) $operationBase = $operationBase->add($component);
        $discount = Money::fromDecimal($input->discount); $operationBase = Money::fromMinor(max(0,$operationBase->minorAmount()-$discount->minorAmount()));
        if ($operationBase->minorAmount() <= 0) throw new InvalidArgumentException('A base da operação deve ser maior que zero.');

        $main = $this->calculateItem('Item principal',$operationBase,$usedMva,$internalRate,$interstateRate,$fcpRate,$input->ownIcmsOverride);
        $items = [$main];
        foreach ($input->items as $item) {
            $value = Money::fromDecimal($item['merchandise_value']); if ($value->minorAmount() <= 0) continue;
            $itemOriginalMva = Percentage::fromString($item['mva'] !== '' ? $item['mva'] : $input->originalMva);
            $itemUsedMva = $input->operationType === 'interstate' && $input->adjustMva ? $this->adjustedMva($itemOriginalMva,$interstateRate,$internalRate) : $itemOriginalMva;
            $items[] = $this->calculateItem($item['description'] !== '' ? $item['description'] : 'Item adicional',$value,$itemUsedMva,$internalRate,$interstateRate,$fcpRate,'');
        }

        $totals = ['operation_base_minor'=>0,'st_base_minor'=>0,'own_icms_minor'=>0,'icms_st_minor'=>0,'fcp_st_minor'=>0,'total_minor'=>0];
        foreach ($items as $item) foreach ($totals as $key => $_) $totals[$key] += $item[$key];

        $memory = new CalculationMemory('1.0.0',[
            new CalculationMemoryStep('operation_base','Base da operação','mercadoria + frete + seguro + outras despesas + IPI informado − desconto incondicional',['components'=>'informados pelo usuário'],$operationBase->minorAmount()),
            new CalculationMemoryStep('mva','MVA utilizada',$input->operationType === 'interstate' && $input->adjustMva ? '[(1 + MVA original) × (1 − alíquota interestadual) ÷ (1 − alíquota interna)] − 1' : 'MVA original informada',['original_mva'=>$originalMva->toDecimalString(),'used_mva'=>$usedMva->toDecimalString()],$usedMva->toDecimalString().'%'),
            new CalculationMemoryStep('st_base','Base ICMS-ST','base da operação × (1 + MVA utilizada)',['items'=>count($items)],$totals['st_base_minor']),
            new CalculationMemoryStep('icms_st','ICMS-ST','(base ICMS-ST × alíquota interna) − ICMS próprio',['internal_rate'=>$internalRate->toDecimalString(),'own_rate'=>$interstateRate->toDecimalString()],$totals['icms_st_minor'],'Resultado limitado a zero.'),
            new CalculationMemoryStep('fcp','FCP-ST','base ICMS-ST × alíquota FCP informada',['fcp_rate'=>$fcpRate->toDecimalString()],$totals['fcp_st_minor']),
        ],[NormativeRuleSnapshot::fromRule($this->rule,$referenceDate)],[
            'A ferramenta é paramétrica: MVA, alíquotas, FCP e enquadramento do produto devem ser confirmados na legislação da UF de destino e nos acordos/protocolos aplicáveis.',
            'A composição da base pode variar por mercadoria, operação, benefício, redução, pauta/PMPF e legislação estadual. Informe apenas parcelas que efetivamente integrem a base do caso.',
            'O cálculo não classifica NCM/CEST nem determina automaticamente se a mercadoria está sujeita à substituição tributária.',
        ],true);

        return new ToolCalculationResult('calculadora-icms-st','1.0.0',[
            new ToolCalculationSummaryItem('st_base','Base ICMS-ST',Money::fromMinor($totals['st_base_minor'])->formatPtBr()),
            new ToolCalculationSummaryItem('icms_st','ICMS-ST estimado',Money::fromMinor($totals['icms_st_minor'])->formatPtBr()),
            new ToolCalculationSummaryItem('fcp_st','FCP-ST',Money::fromMinor($totals['fcp_st_minor'])->formatPtBr()),
            new ToolCalculationSummaryItem('total','Total estimado',Money::fromMinor($totals['total_minor'])->formatPtBr()),
        ],[
            'input'=>$input->toArray(),'operation_type'=>$input->operationType,'original_mva'=>$originalMva->toDecimalString(),'used_mva'=>$usedMva->toDecimalString(),
            'internal_rate'=>$internalRate->toDecimalString(),'interstate_rate'=>$interstateRate->toDecimalString(),'fcp_rate'=>$fcpRate->toDecimalString(),'items'=>$items,'totals'=>$totals,
        ],[
            new ToolCalculationWarning('state_rules','ICMS-ST e FCP dependem da UF, NCM/CEST, segmento, benefício e protocolo/convênio. Confirme todos os parâmetros antes do recolhimento.',ToolCalculationWarningLevel::Info),
            new ToolCalculationWarning('estimate','O resultado é uma estimativa paramétrica e não substitui a validação fiscal da operação e do documento eletrônico.',ToolCalculationWarningLevel::Info),
        ],calculationMemory:$memory);
    }

    private function calculateItem(string $description, Money $base, Percentage $mva, Percentage $internalRate, Percentage $ownRate, Percentage $fcpRate, string $ownOverride): array
    {
        $denominator = 100_000_000;
        $numerator = $denominator + $mva->millionthsOfPercent();
        $stMinor = intdiv(($base->minorAmount() * $numerator) + intdiv($denominator,2), $denominator);
        $stBase = Money::fromMinor($stMinor);
        $own = $ownOverride !== '' ? Money::fromDecimal($ownOverride) : $base->percentage($ownRate);
        $internal = $stBase->percentage($internalRate);
        $icmsSt = max(0,$internal->minorAmount()-$own->minorAmount());
        $fcp = $stBase->percentage($fcpRate)->minorAmount();
        return ['description'=>$description,'operation_base_minor'=>$base->minorAmount(),'mva'=>$mva->toDecimalString(),'st_base_minor'=>$stBase->minorAmount(),'own_icms_minor'=>$own->minorAmount(),'icms_internal_minor'=>$internal->minorAmount(),'icms_st_minor'=>$icmsSt,'fcp_st_minor'=>$fcp,'total_minor'=>$icmsSt+$fcp];
    }

    private function adjustedMva(Percentage $original, Percentage $interstate, Percentage $internal): Percentage
    {
        $scale = 100_000_000;
        $orig = $original->millionthsOfPercent();
        $inter = $interstate->millionthsOfPercent();
        $intra = $internal->millionthsOfPercent();
        if ($intra >= $scale) throw new InvalidArgumentException('Alíquota interna inválida para ajuste da MVA.');
        $numerator = ($scale + $orig) * ($scale - $inter);
        $denominator = $scale - $intra;
        $gross = intdiv($numerator + intdiv($denominator, 2), $denominator);
        $adjusted = max(0, $gross - $scale);
        $whole = intdiv($adjusted, 1_000_000);
        $fraction = rtrim(str_pad((string) ($adjusted % 1_000_000), 6, '0', STR_PAD_LEFT), '0');
        return Percentage::fromString($fraction === '' ? (string) $whole : $whole.'.'.$fraction);
    }
}
