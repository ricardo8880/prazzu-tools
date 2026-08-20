<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Domain\Services;

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
use App\Tools\TaxReformSimulator\Application\Data\CalculationInput;
use App\Tools\TaxReformSimulator\Domain\Rules\ConsumptionTaxTransitionRule;
use InvalidArgumentException;

final readonly class Calculator implements ToolCalculator
{
    public function __construct(private ConsumptionTaxTransitionRule $rule) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada inválida.');
        }

        $revenue = Money::fromDecimal($input->revenue);
        $legacyFederalRate = Percentage::fromString($input->legacyFederalRate);
        $legacySubnationalRate = Percentage::fromString($input->legacySubnationalRate);
        $cbsReferenceRate = Percentage::fromString($input->cbsReferenceRate);
        $ibsReferenceRate = Percentage::fromString($input->ibsReferenceRate);
        $creditBasePercent = Percentage::fromString($input->creditBasePercent);
        $transition = $this->rule->forYear($input->year);

        $baselineFederal = $revenue->percentage($legacyFederalRate);
        $baselineSubnational = $revenue->percentage($legacySubnationalRate);
        $baseline = $baselineFederal->add($baselineSubnational);
        $creditBase = $revenue->percentage($creditBasePercent);

        $legacyFederal = $revenue->percentage($this->scaledPercentage(
            intdiv($legacyFederalRate->millionthsOfPercent() * $transition['lf'], 100),
        ));
        $legacySubnational = $revenue->percentage($this->scaledPercentage(
            intdiv($legacySubnationalRate->millionthsOfPercent() * $transition['ls'], 100),
        ));

        if ($input->year === 2026) {
            $cbsRate = Percentage::fromString('0.9');
            $ibsRate = Percentage::fromString('0.1');
        } elseif ($input->year <= 2028) {
            $cbsRate = $this->scaledPercentage(max(0, $cbsReferenceRate->millionthsOfPercent() - 100000));
            $ibsRate = Percentage::fromString('0.1');
        } else {
            $cbsRate = $cbsReferenceRate;
            $ibsRate = $this->scaledPercentage(
                intdiv($ibsReferenceRate->millionthsOfPercent() * $transition['ibs'], 100),
            );
        }

        $cbsAmount = $this->netTaxAfterApproximateCredits($revenue, $creditBase, $cbsRate);
        $ibsAmount = $this->netTaxAfterApproximateCredits($revenue, $creditBase, $ibsRate);
        $testTaxes = $cbsAmount->add($ibsAmount);
        $transitionOffset = Money::zero();

        if ($input->year === 2026) {
            // EC 132/2023 e LC 214/2025 tratam 2026 como ano-teste. Quando houver
            // recolhimento de CBS/IBS, o montante é compensável contra PIS/Cofins e,
            // se insuficiente, contra outros tributos federais ou por ressarcimento.
            // Como este simulador recebe uma carga federal legada agregada, modelamos
            // a compensação até o limite dessa carga e não somamos a alíquota-teste
            // como custo adicional automático.
            $transitionOffset = Money::fromMinor(min(
                $legacyFederal->minorAmount(),
                $testTaxes->minorAmount(),
            ));
            $legacyFederal = $legacyFederal->subtract($transitionOffset);
        }

        $total = $legacyFederal
            ->add($legacySubnational)
            ->add($cbsAmount)
            ->add($ibsAmount);
        $delta = $total->subtract($baseline);

        $assumptions = [
            'Alíquotas futuras de referência são informadas pelo usuário; não são inventadas pela ferramenta.',
            'Créditos são aproximados pela parcela da receita indicada como base de crédito e não substituem a apuração documento a documento.',
        ];

        if ($input->year === 2026) {
            $assumptions[] = 'Em 2026, CBS/IBS são alíquotas de teste. O simulador neutraliza seu efeito até o limite da carga federal legada informada, representando a compensação legal; eventual dispensa por cumprimento das obrigações acessórias produz o mesmo efeito econômico agregado nesta comparação.';
        }

        $memory = new CalculationMemory(
            '1.1.0',
            [
                new CalculationMemoryStep(
                    'legacy',
                    'Legado remanescente',
                    $input->year === 2026
                        ? 'carga federal legada − compensação da alíquota-teste + carga subnacional'
                        : 'carga efetiva × percentual remanescente da transição',
                    [
                        'year' => $input->year,
                        'federal_before_offset_minor' => $baselineFederal->minorAmount(),
                        'transition_offset_minor' => $transitionOffset->minorAmount(),
                    ],
                    $legacyFederal->add($legacySubnational)->minorAmount(),
                ),
                new CalculationMemoryStep(
                    'new',
                    'CBS + IBS líquidos',
                    'débitos de referência menos créditos aproximados',
                    [
                        'cbs_rate' => $cbsRate->toDecimalString(),
                        'ibs_rate' => $ibsRate->toDecimalString(),
                        'credit_base_percent' => $creditBasePercent->toDecimalString(),
                    ],
                    $testTaxes->minorAmount(),
                ),
            ],
            [NormativeRuleSnapshot::fromRule(
                $this->rule,
                ReferenceDate::fromString($input->year.'-01-01'),
            )],
            $assumptions,
            true,
        );

        $warnings = [
            new ToolCalculationWarning('transition', $transition['note'], ToolCalculationWarningLevel::Info),
            new ToolCalculationWarning(
                'parametric',
                'Não contempla regimes específicos, reduções, Imposto Seletivo, cashback ou classificação jurídica das operações.',
                ToolCalculationWarningLevel::Info,
            ),
        ];

        if ($input->year === 2026) {
            $warnings[] = new ToolCalculationWarning(
                'test_year_offset',
                '2026 é ano-teste: CBS/IBS não são tratados como acréscimo automático à carga. A simulação aplica compensação contra a carga federal legada informada; contribuintes que cumpram as obrigações acessórias podem estar dispensados do recolhimento, conforme a legislação aplicável.',
                ToolCalculationWarningLevel::Info,
            );
        }

        return new ToolCalculationResult(
            'simulador-reforma-tributaria-consumo',
            '1.1.0',
            [
                new ToolCalculationSummaryItem('baseline', 'Carga atual informada', $baseline->formatPtBr()),
                new ToolCalculationSummaryItem('transition', 'Carga simulada '.$input->year, $total->formatPtBr()),
                new ToolCalculationSummaryItem('delta', 'Diferença estimada', $delta->formatPtBr()),
                new ToolCalculationSummaryItem('new_taxes', 'CBS + IBS líquidos', $testTaxes->formatPtBr()),
            ],
            [
                'year' => $input->year,
                'cbs_rate' => $cbsRate->toDecimalString(),
                'ibs_rate' => $ibsRate->toDecimalString(),
                'legacy_federal_remaining_percent' => $transition['lf'],
                'legacy_subnational_remaining_percent' => $transition['ls'],
                'transition_offset_minor' => $transitionOffset->minorAmount(),
                'transition_note' => $transition['note'],
                'rule_version' => ConsumptionTaxTransitionRule::VERSION,
            ],
            $warnings,
            calculationMemory: $memory,
        );
    }

    private function netTaxAfterApproximateCredits(Money $revenue, Money $creditBase, Percentage $rate): Money
    {
        return Money::fromMinor(max(
            0,
            $revenue->percentage($rate)->minorAmount() - $creditBase->percentage($rate)->minorAmount(),
        ));
    }

    private function scaledPercentage(int $scaled): Percentage
    {
        $whole = intdiv($scaled, 1000000);
        $fraction = str_pad((string) ($scaled % 1000000), 6, '0', STR_PAD_LEFT);

        return Percentage::fromString($whole.'.'.$fraction);
    }
}
