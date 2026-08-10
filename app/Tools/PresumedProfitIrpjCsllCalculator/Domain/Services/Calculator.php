<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Normative\NormativeRuleResolver;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\PresumedProfitIrpjCsllCalculator\Application\Data\CalculationInput;
use App\Tools\PresumedProfitIrpjCsllCalculator\Domain\Rules\PresumedProfitRule;
use App\Tools\PresumedProfitIrpjCsllCalculator\Domain\Rules\RuleCatalog;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a calculadora de IRPJ e CSLL no Lucro Presumido.');
        }

        $domain = $input->toDomain();
        if ($domain->quarter < 1 || $domain->quarter > 4) {
            throw new InvalidArgumentException('Trimestre inválido.');
        }

        if ($domain->quarter === 1 && $domain->priorIrpjPresumptionRevenue->minorAmount() > 0) {
            throw new InvalidArgumentException('O 1º trimestre não aceita receita anterior para o limite do IRPJ.');
        }
        if ($domain->quarter <= 2 && $domain->priorCsllPresumptionRevenue->minorAmount() > 0) {
            throw new InvalidArgumentException('A receita anterior da CSLL somente se aplica a partir do 3º trimestre de 2026.');
        }

        $referenceDate = ReferenceDate::fromString(sprintf('2026-%02d-%02d', $domain->quarter * 3, $domain->quarter === 1 ? 31 : ($domain->quarter === 2 ? 30 : ($domain->quarter === 3 ? 30 : 31))));
        $rule = (new NormativeRuleResolver)->resolveCurrent(RuleCatalog::all(), 'lucro_presumido.irpj_csll', $referenceDate);
        assert($rule instanceof PresumedProfitRule);

        $totalRevenue = Money::zero();
        foreach ($domain->activityRevenue as $revenue) {
            $totalRevenue = $totalRevenue->add($revenue);
        }
        if ($totalRevenue->minorAmount() <= 0) {
            throw new InvalidArgumentException('Informe receita bruta em ao menos uma atividade.');
        }

        $irpjNormalAllowance = $this->currentNormalAllowance($rule->irpjCumulativeNormalLimit($domain->quarter), $domain->priorIrpjPresumptionRevenue, $totalRevenue);
        $csllNormalAllowance = $domain->quarter === 1
            ? $totalRevenue
            : $this->currentNormalAllowance($rule->csllCumulativeNormalLimit($domain->quarter), $domain->priorCsllPresumptionRevenue, $totalRevenue);

        $irpjPresumedBase = Money::zero();
        $csllPresumedBase = Money::zero();
        $activityDetails = [];

        foreach ($rule->activityProfiles() as $key => $profile) {
            $revenue = $domain->activityRevenue[$key] ?? Money::zero();
            if ($revenue->minorAmount() === 0) {
                continue;
            }

            $irpjNormalRevenue = $this->proportionalShare($revenue, $totalRevenue, $irpjNormalAllowance);
            $irpjExcessRevenue = $revenue->subtract($irpjNormalRevenue);
            $csllNormalRevenue = $this->proportionalShare($revenue, $totalRevenue, $csllNormalAllowance);
            $csllExcessRevenue = $revenue->subtract($csllNormalRevenue);

            $irpjBase = $irpjNormalRevenue->percentage(Percentage::fromString($profile['irpj']))
                ->add($irpjExcessRevenue->percentage(Percentage::fromString($profile['irpj_increased'])));
            $csllBase = $csllNormalRevenue->percentage(Percentage::fromString($profile['csll']))
                ->add($csllExcessRevenue->percentage(Percentage::fromString($profile['csll_increased'])));

            $irpjPresumedBase = $irpjPresumedBase->add($irpjBase);
            $csllPresumedBase = $csllPresumedBase->add($csllBase);
            $activityDetails[$key] = [
                'label' => $profile['label'],
                'revenue_minor' => $revenue->minorAmount(),
                'irpj_normal_rate' => $profile['irpj'],
                'irpj_increased_rate' => $profile['irpj_increased'],
                'irpj_normal_revenue_minor' => $irpjNormalRevenue->minorAmount(),
                'irpj_excess_revenue_minor' => $irpjExcessRevenue->minorAmount(),
                'irpj_base_minor' => $irpjBase->minorAmount(),
                'csll_normal_rate' => $profile['csll'],
                'csll_increased_rate' => $profile['csll_increased'],
                'csll_normal_revenue_minor' => $csllNormalRevenue->minorAmount(),
                'csll_excess_revenue_minor' => $csllExcessRevenue->minorAmount(),
                'csll_base_minor' => $csllBase->minorAmount(),
            ];
        }

        $irpjBase = $irpjPresumedBase->add($domain->otherTaxableAdditions);
        $csllBase = $csllPresumedBase->add($domain->otherTaxableAdditions);
        $irpjMain = $irpjBase->percentage(Percentage::fromString('15'));
        $additionalThreshold = Money::fromDecimal('60000');
        $additionalBase = $irpjBase->minorAmount() > $additionalThreshold->minorAmount()
            ? $irpjBase->subtract($additionalThreshold)
            : Money::zero();
        $irpjAdditional = $additionalBase->percentage(Percentage::fromString('10'));
        $irpjBeforeCredits = $irpjMain->add($irpjAdditional);
        $csllBeforeCredits = $csllBase->percentage(Percentage::fromString('9'));
        $irpjDue = $this->subtractFloorZero($irpjBeforeCredits, $domain->irpjCredits);
        $csllDue = $this->subtractFloorZero($csllBeforeCredits, $domain->csllCredits);
        $totalDue = $irpjDue->add($csllDue);

        $memory = new CalculationMemory(
            schemaVersion: '1.0.0',
            steps: [
                new CalculationMemoryStep('presumption_limit', 'Faixa normal de presunção no trimestre', 'limite acumulado aplicável − receita de presunção dos trimestres anteriores, limitado à receita atual', ['quarter' => $domain->quarter, 'prior_irpj_minor' => $domain->priorIrpjPresumptionRevenue->minorAmount(), 'prior_csll_minor' => $domain->priorCsllPresumptionRevenue->minorAmount()], 'IRPJ '.$irpjNormalAllowance->formatPtBr().' | CSLL '.$csllNormalAllowance->formatPtBr()),
                new CalculationMemoryStep('irpj_base', 'Base do IRPJ', 'soma das bases presumidas por atividade + adições tributáveis integralmente', ['presumed_base_minor' => $irpjPresumedBase->minorAmount(), 'additions_minor' => $domain->otherTaxableAdditions->minorAmount()], $irpjBase->minorAmount(), 'Arredondamento HalfUp em centavos a cada aplicação percentual.'),
                new CalculationMemoryStep('irpj', 'IRPJ', '15% da base + adicional de 10% sobre a parcela da base trimestral acima de R$ 60.000,00 − créditos informados', ['base_minor' => $irpjBase->minorAmount(), 'credits_minor' => $domain->irpjCredits->minorAmount()], $irpjDue->minorAmount(), 'Saldo nunca inferior a zero.'),
                new CalculationMemoryStep('csll_base', 'Base da CSLL', 'soma das bases presumidas por atividade + adições tributáveis integralmente', ['presumed_base_minor' => $csllPresumedBase->minorAmount(), 'additions_minor' => $domain->otherTaxableAdditions->minorAmount()], $csllBase->minorAmount(), 'Arredondamento HalfUp em centavos a cada aplicação percentual.'),
                new CalculationMemoryStep('csll', 'CSLL', '9% da base − créditos informados', ['base_minor' => $csllBase->minorAmount(), 'credits_minor' => $domain->csllCredits->minorAmount()], $csllDue->minorAmount(), 'Saldo nunca inferior a zero.'),
            ],
            normativeRules: [NormativeRuleSnapshot::fromRule($rule, $referenceDate)],
            assumptions: [
                'Escopo para pessoas jurídicas em geral tributadas pelo lucro presumido em 2026; instituições financeiras e regimes/setores com alíquotas ou bases específicas não estão cobertos.',
                'Receitas financeiras, ganhos de capital e demais valores informados como adições entram integralmente nas bases, mas não consomem o limite de R$ 5 milhões da LC 224/2025.',
                'Para múltiplas atividades, a faixa normal disponível é distribuída proporcionalmente à receita bruta de cada atividade, conforme orientação da Receita Federal.',
                'No IRPJ, o acréscimo de 10% nos percentuais de presunção vale desde o 1º trimestre de 2026; na CSLL, desde o 2º trimestre. Para 2026, o limite anual da CSLL é R$ 3,75 milhões.',
                'Créditos/retensões somente devem ser informados quando juridicamente compensáveis no período; a ferramenta não valida a origem do crédito.',
            ],
            isEstimate: true,
        );

        return new ToolCalculationResult(
            toolSlug: 'calculadora-irpj-csll-lucro-presumido',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('irpj_base', 'Base IRPJ', $irpjBase->formatPtBr()),
                new ToolCalculationSummaryItem('irpj_due', 'IRPJ a pagar', $irpjDue->formatPtBr()),
                new ToolCalculationSummaryItem('csll_base', 'Base CSLL', $csllBase->formatPtBr()),
                new ToolCalculationSummaryItem('csll_due', 'CSLL a pagar', $csllDue->formatPtBr()),
                new ToolCalculationSummaryItem('total_due', 'IRPJ + CSLL', $totalDue->formatPtBr()),
            ],
            details: [
                'input' => $input->toArray(),
                'total_revenue_minor' => $totalRevenue->minorAmount(),
                'irpj_normal_allowance_minor' => $irpjNormalAllowance->minorAmount(),
                'csll_normal_allowance_minor' => $csllNormalAllowance->minorAmount(),
                'activities' => $activityDetails,
                'irpj_presumed_base_minor' => $irpjPresumedBase->minorAmount(),
                'csll_presumed_base_minor' => $csllPresumedBase->minorAmount(),
                'other_taxable_additions_minor' => $domain->otherTaxableAdditions->minorAmount(),
                'irpj_base_minor' => $irpjBase->minorAmount(),
                'irpj_main_minor' => $irpjMain->minorAmount(),
                'irpj_additional_base_minor' => $additionalBase->minorAmount(),
                'irpj_additional_minor' => $irpjAdditional->minorAmount(),
                'irpj_before_credits_minor' => $irpjBeforeCredits->minorAmount(),
                'irpj_credits_minor' => $domain->irpjCredits->minorAmount(),
                'irpj_due_minor' => $irpjDue->minorAmount(),
                'csll_base_minor' => $csllBase->minorAmount(),
                'csll_before_credits_minor' => $csllBeforeCredits->minorAmount(),
                'csll_credits_minor' => $domain->csllCredits->minorAmount(),
                'csll_due_minor' => $csllDue->minorAmount(),
                'total_due_minor' => $totalDue->minorAmount(),
            ],
            warnings: [
                new ToolCalculationWarning('scope', 'Confirme se a atividade realmente se enquadra no percentual selecionado. Serviços hospitalares, saúde, construção, atividades regulamentadas e situações especiais podem exigir requisitos adicionais.', ToolCalculationWarningLevel::Info),
                new ToolCalculationWarning('2026_change', 'Em 2026, a LC 224/2025 elevou em 10% os percentuais de presunção sobre a parcela da receita que excede os limites aplicáveis; informe corretamente as receitas anteriores para aproveitar os ajustes entre trimestres.', ToolCalculationWarningLevel::Info),
            ],
            calculationMemory: $memory,
        );
    }

    private function currentNormalAllowance(Money $cumulativeLimit, Money $priorRevenue, Money $currentRevenue): Money
    {
        $remaining = max(0, $cumulativeLimit->minorAmount() - $priorRevenue->minorAmount());
        return Money::fromMinor(min($remaining, $currentRevenue->minorAmount()));
    }

    private function proportionalShare(Money $activityRevenue, Money $totalRevenue, Money $allowance): Money
    {
        if ($allowance->minorAmount() >= $totalRevenue->minorAmount()) {
            return $activityRevenue;
        }
        if ($allowance->minorAmount() === 0) {
            return Money::zero();
        }

        $activityMinor = $activityRevenue->minorAmount();
        $allowanceMinor = $allowance->minorAmount();
        $totalMinor = $totalRevenue->minorAmount();

        $gcd = $this->gcd($activityMinor, $totalMinor);
        $activityMinor = intdiv($activityMinor, $gcd);
        $totalMinor = intdiv($totalMinor, $gcd);
        $gcd = $this->gcd($allowanceMinor, $totalMinor);
        $allowanceMinor = intdiv($allowanceMinor, $gcd);
        $totalMinor = intdiv($totalMinor, $gcd);

        if ($activityMinor !== 0 && $allowanceMinor > intdiv(PHP_INT_MAX, $activityMinor)) {
            throw new InvalidArgumentException('Receitas fora do intervalo seguro para rateio proporcional.');
        }

        $minor = IntegerRounding::divide(
            $activityMinor * $allowanceMinor,
            $totalMinor,
            RoundingMode::HalfUp,
        );

        return Money::fromMinor(min($minor, $activityRevenue->minorAmount()));
    }

    private function gcd(int $left, int $right): int
    {
        $left = abs($left);
        $right = abs($right);

        while ($right !== 0) {
            [$left, $right] = [$right, $left % $right];
        }

        return max(1, $left);
    }

    private function subtractFloorZero(Money $gross, Money $credits): Money
    {
        return Money::fromMinor(max(0, $gross->minorAmount() - $credits->minorAmount()));
    }
}
