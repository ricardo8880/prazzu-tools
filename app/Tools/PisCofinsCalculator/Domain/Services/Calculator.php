<?php

declare(strict_types=1);

namespace App\Tools\PisCofinsCalculator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Money\Money;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\PisCofinsCalculator\Application\Data\CalculationInput;
use App\Tools\PisCofinsCalculator\Domain\Rules\PisCofinsRule;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function __construct(private readonly PisCofinsRule $rule = new PisCofinsRule()) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) throw new InvalidArgumentException('Entrada incompatível com a ferramenta calculadora-pis-cofins.');
        if (! in_array($input->regime, ['cumulative','non_cumulative'], true)) throw new InvalidArgumentException('Regime de apuração inválido.');
        $referenceDate = ReferenceDate::fromString($input->period.'-01');
        if (! $this->rule->effectivePeriod()->contains($referenceDate)) throw new InvalidArgumentException('Esta versão normativa atende competências de 2026.');

        $mainRevenue = Money::fromDecimal($input->taxableRevenue);
        $revenue = $mainRevenue;
        $creditBase = Money::fromDecimal($input->creditBase);
        $operations = [];
        foreach ($input->operations as $operation) {
            $opRevenue = Money::fromDecimal($operation['revenue']);
            $opCredit = Money::fromDecimal($operation['credit_base']);
            if ($opRevenue->minorAmount() === 0 && $opCredit->minorAmount() === 0) continue;
            $revenue = $revenue->add($opRevenue); $creditBase = $creditBase->add($opCredit);
            $operations[] = ['description'=>$operation['description'] !== '' ? $operation['description'] : 'Operação adicional','revenue_minor'=>$opRevenue->minorAmount(),'credit_base_minor'=>$opCredit->minorAmount()];
        }
        if ($revenue->minorAmount() <= 0) throw new InvalidArgumentException('A base tributável total deve ser maior que zero.');

        $pisWithheld = Money::fromDecimal($input->pisWithheld); $cofinsWithheld = Money::fromDecimal($input->cofinsWithheld);
        $selected = $this->calculateRegime($input->regime, $revenue, $creditBase, $pisWithheld, $cofinsWithheld);
        $cumulative = $this->calculateRegime('cumulative', $revenue, $creditBase, $pisWithheld, $cofinsWithheld);
        $nonCumulative = $this->calculateRegime('non_cumulative', $revenue, $creditBase, $pisWithheld, $cofinsWithheld);

        $memory = new CalculationMemory('1.0.0', [
            new CalculationMemoryStep('base','Base total','base tributável principal + bases tributáveis das operações adicionais',['main_revenue_minor'=>$mainRevenue->minorAmount(),'operations_count'=>count($operations)],$revenue->minorAmount()),
            new CalculationMemoryStep('pis_debit','Débito de PIS/Pasep','base tributável × alíquota do regime',['base_minor'=>$revenue->minorAmount(),'rate'=>$selected['pis_rate']],$selected['pis_debit_minor'],'HalfUp em centavos.'),
            new CalculationMemoryStep('cofins_debit','Débito de Cofins','base tributável × alíquota do regime',['base_minor'=>$revenue->minorAmount(),'rate'=>$selected['cofins_rate']],$selected['cofins_debit_minor'],'HalfUp em centavos.'),
            new CalculationMemoryStep('credits','Créditos','base elegível a créditos × alíquota correspondente',['credit_base_minor'=>$creditBase->minorAmount(),'regime'=>$input->regime],$selected['credits_total_minor'],'No cumulativo, crédito geral igual a zero.'),
            new CalculationMemoryStep('payable','Contribuições a recolher','débitos − créditos − retenções/compensações confirmadas, limitado a zero',['pis_withheld_minor'=>$pisWithheld->minorAmount(),'cofins_withheld_minor'=>$cofinsWithheld->minorAmount()],$selected['total_payable_minor'],'Saldo negativo é exibido como saldo credor/excedente.'),
        ], [NormativeRuleSnapshot::fromRule($this->rule, $referenceDate)], [
            'A base tributável é informada já após exclusões e ajustes aplicáveis; a ferramenta não classifica NCM, CST, monofasia, alíquota zero, suspensão, isenção ou regime especial.',
            'No não cumulativo, informe somente bases que efetivamente gerem crédito segundo a legislação aplicável.',
            'Alíquotas gerais: 0,65%/3% no cumulativo e 1,65%/7,6% no não cumulativo.',
            'Em 2026, CBS e IBS estão em transição. Esta ferramenta apura PIS/Cofins e não soma CBS/IBS como ônus adicional.',
        ], true);

        return new ToolCalculationResult('calculadora-pis-cofins','1.0.0',[
            new ToolCalculationSummaryItem('pis_payable','PIS/Pasep a recolher',Money::fromMinor($selected['pis_payable_minor'])->formatPtBr()),
            new ToolCalculationSummaryItem('cofins_payable','Cofins a recolher',Money::fromMinor($selected['cofins_payable_minor'])->formatPtBr()),
            new ToolCalculationSummaryItem('total_payable','Total a recolher',Money::fromMinor($selected['total_payable_minor'])->formatPtBr()),
            new ToolCalculationSummaryItem('effective_rate','Alíquota efetiva sobre a base',$this->effectiveRate($selected['contribution_before_withholding_minor'],$revenue->minorAmount()).'%'),
        ], [
            'input'=>$input->toArray(),'regime'=>$input->regime,'compare_regimes'=>$input->compareRegimes,'revenue_minor'=>$revenue->minorAmount(),'credit_base_minor'=>$creditBase->minorAmount(),'operations'=>$operations,'selected'=>$selected,
            'comparison'=>['cumulative'=>$cumulative,'non_cumulative'=>$nonCumulative,'difference_minor'=>$cumulative['contribution_before_withholding_minor']-$nonCumulative['contribution_before_withholding_minor']],
        ], [
            new ToolCalculationWarning('scope','Confirme o enquadramento tributário da receita. Operações monofásicas, alíquota zero, suspensão, substituição tributária, importação, benefícios e regimes setoriais podem exigir tratamento diferente.',ToolCalculationWarningLevel::Info),
            new ToolCalculationWarning('transition_2026','Em 2026, CBS e IBS estão em fase de teste/transição. Considere as regras oficiais de compensação com PIS/Cofins e as obrigações acessórias aplicáveis.',ToolCalculationWarningLevel::Info),
        ], calculationMemory:$memory);
    }

    private function calculateRegime(string $regime, Money $revenue, Money $creditBase, Money $pisWithheld, Money $cofinsWithheld): array
    {
        $non = $regime === 'non_cumulative';
        $pisRate = $non ? $this->rule->nonCumulativePisRate() : $this->rule->cumulativePisRate();
        $cofinsRate = $non ? $this->rule->nonCumulativeCofinsRate() : $this->rule->cumulativeCofinsRate();
        $pisDebit = $revenue->percentage($pisRate); $cofinsDebit = $revenue->percentage($cofinsRate);
        $pisCredit = $non ? $creditBase->percentage($pisRate) : Money::zero(); $cofinsCredit = $non ? $creditBase->percentage($cofinsRate) : Money::zero();
        $pisBefore = max(0,$pisDebit->minorAmount()-$pisCredit->minorAmount()); $cofinsBefore = max(0,$cofinsDebit->minorAmount()-$cofinsCredit->minorAmount());
        $pisPayable = max(0,$pisBefore-$pisWithheld->minorAmount()); $cofinsPayable = max(0,$cofinsBefore-$cofinsWithheld->minorAmount());
        return ['label'=>$non?'Não cumulativo':'Cumulativo','pis_rate'=>$pisRate->toDecimalString(),'cofins_rate'=>$cofinsRate->toDecimalString(),'pis_debit_minor'=>$pisDebit->minorAmount(),'cofins_debit_minor'=>$cofinsDebit->minorAmount(),'pis_credit_minor'=>$pisCredit->minorAmount(),'cofins_credit_minor'=>$cofinsCredit->minorAmount(),'credits_total_minor'=>$pisCredit->minorAmount()+$cofinsCredit->minorAmount(),'pis_credit_balance_minor'=>max(0,$pisCredit->minorAmount()-$pisDebit->minorAmount()),'cofins_credit_balance_minor'=>max(0,$cofinsCredit->minorAmount()-$cofinsDebit->minorAmount()),'pis_before_withholding_minor'=>$pisBefore,'cofins_before_withholding_minor'=>$cofinsBefore,'contribution_before_withholding_minor'=>$pisBefore+$cofinsBefore,'pis_withheld_minor'=>$pisWithheld->minorAmount(),'cofins_withheld_minor'=>$cofinsWithheld->minorAmount(),'pis_payable_minor'=>$pisPayable,'cofins_payable_minor'=>$cofinsPayable,'total_payable_minor'=>$pisPayable+$cofinsPayable];
    }

    private function effectiveRate(int $taxMinor, int $revenueMinor): string
    {
        if ($revenueMinor <= 0) return '0,00';
        $hundredths = intdiv(($taxMinor * 10000) + intdiv($revenueMinor,2), $revenueMinor);
        return number_format($hundredths / 100,2,',','.');
    }
}
