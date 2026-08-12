<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Domain\Services;

use App\Core\Math\IntegerRounding;
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
use App\Tools\ProfitDistributionBalanceSimulator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com o simulador de distribuição de lucros.');
        }

        $revenue = Money::fromDecimal($input->annualRevenue);
        $informedProfit = Money::fromDecimal($input->accountingProfit);
        $taxes = Money::fromDecimal($input->taxesOnRevenue);
        $prior = Money::fromDecimal($input->priorDistributions);
        $proLabore = Money::fromDecimal($input->monthlyProLabore);
        $operatingExpenses = Money::fromDecimal($input->operatingExpenses);
        $otherExpenses = Money::fromDecimal($input->otherExpenses);
        $referenceMargin = Percentage::fromString($input->referenceMargin);
        $growth = Percentage::fromString($input->monthlyGrowthRate);

        if (
            $revenue->minorAmount() <= 0
            || $informedProfit->minorAmount() < 0
            || $taxes->minorAmount() < 0
            || $prior->minorAmount() < 0
            || $proLabore->minorAmount() < 0
            || $operatingExpenses->minorAmount() < 0
            || $otherExpenses->minorAmount() < 0
            || $input->planningMonths < 1
            || $input->planningMonths > 24
        ) {
            throw new InvalidArgumentException('Parâmetros inválidos.');
        }

        $annualProLabore = $proLabore->minorAmount() * 12;
        $simulatedProfit = max(
            0,
            $revenue->minorAmount()
                - $taxes->minorAmount()
                - $annualProLabore
                - $operatingExpenses->minorAmount()
                - $otherExpenses->minorAmount(),
        );
        $accountingProfitMinor = $input->simulateBookkeeping ? $simulatedProfit : $informedProfit->minorAmount();

        $withoutBalanceGross = $revenue->percentage($referenceMargin)->minorAmount();
        $withoutBalance = max(0, $withoutBalanceGross - $taxes->minorAmount() - $prior->minorAmount());
        $withBalance = max(0, $accountingProfitMinor - $prior->minorAmount());
        $advantage = $withBalance - $withoutBalance;

        $profitBasisPoints = $revenue->minorAmount() > 0
            ? IntegerRounding::divide($accountingProfitMinor * 10_000, $revenue->minorAmount())
            : 0;
        $taxBasisPoints = $revenue->minorAmount() > 0
            ? IntegerRounding::divide($taxes->minorAmount() * 10_000, $revenue->minorAmount())
            : 0;
        $operatingExpenseBasisPoints = $revenue->minorAmount() > 0
            ? IntegerRounding::divide($operatingExpenses->minorAmount() * 10_000, $revenue->minorAmount())
            : 0;
        $otherExpenseBasisPoints = $revenue->minorAmount() > 0
            ? IntegerRounding::divide($otherExpenses->minorAmount() * 10_000, $revenue->minorAmount())
            : 0;

        $monthlyRevenue = IntegerRounding::divide($revenue->minorAmount(), 12);
        $plan = [];
        $annualPlan = [];
        $cumulativeWithBalance = 0;
        $cumulativeWithoutBalance = 0;
        $cumulativeProLabore = 0;
        $currentRevenue = $monthlyRevenue;

        for ($month = 1; $month <= $input->planningMonths; $month++) {
            if ($month > 1) {
                $currentRevenue += Money::fromMinor($currentRevenue)->percentage($growth)->minorAmount();
            }

            $profit = IntegerRounding::divide($currentRevenue * $profitBasisPoints, 10_000);
            $tax = IntegerRounding::divide($currentRevenue * $taxBasisPoints, 10_000);
            $operating = IntegerRounding::divide($currentRevenue * $operatingExpenseBasisPoints, 10_000);
            $other = IntegerRounding::divide($currentRevenue * $otherExpenseBasisPoints, 10_000);
            $without = max(0, Money::fromMinor($currentRevenue)->percentage($referenceMargin)->minorAmount() - $tax);

            $cumulativeWithBalance += $profit;
            $cumulativeWithoutBalance += $without;
            $cumulativeProLabore += $proLabore->minorAmount();

            $row = [
                'month' => $month,
                'year' => intdiv($month - 1, 12) + 1,
                'revenue_minor' => $currentRevenue,
                'accounting_profit_minor' => $profit,
                'taxes_minor' => $tax,
                'operating_expenses_minor' => $operating,
                'other_expenses_minor' => $other,
                'with_balance_capacity_minor' => $profit,
                'without_balance_capacity_minor' => $without,
                'pro_labore_minor' => $proLabore->minorAmount(),
                'cum_with_balance_minor' => $cumulativeWithBalance,
                'cum_without_balance_minor' => $cumulativeWithoutBalance,
                'cum_pro_labore_minor' => $cumulativeProLabore,
            ];
            $plan[] = $row;

            $yearKey = $row['year'];
            if (! isset($annualPlan[$yearKey])) {
                $annualPlan[$yearKey] = [
                    'year' => $yearKey,
                    'revenue_minor' => 0,
                    'accounting_profit_minor' => 0,
                    'taxes_minor' => 0,
                    'operating_expenses_minor' => 0,
                    'other_expenses_minor' => 0,
                    'pro_labore_minor' => 0,
                    'with_balance_capacity_minor' => 0,
                    'without_balance_capacity_minor' => 0,
                ];
            }

            foreach (['revenue_minor', 'accounting_profit_minor', 'taxes_minor', 'operating_expenses_minor', 'other_expenses_minor', 'pro_labore_minor', 'with_balance_capacity_minor', 'without_balance_capacity_minor'] as $field) {
                $annualPlan[$yearKey][$field] += $row[$field];
            }
        }

        $bookkeeping = [
            'enabled' => $input->simulateBookkeeping,
            'revenue_minor' => $revenue->minorAmount(),
            'taxes_minor' => $taxes->minorAmount(),
            'annual_pro_labore_minor' => $annualProLabore,
            'operating_expenses_minor' => $operatingExpenses->minorAmount(),
            'other_expenses_minor' => $otherExpenses->minorAmount(),
            'informed_accounting_profit_minor' => $informedProfit->minorAmount(),
            'simulated_accounting_profit_minor' => $simulatedProfit,
            'accounting_profit_used_minor' => $accountingProfitMinor,
            'prior_distributions_minor' => $prior->minorAmount(),
            'retained_earnings_after_distributions_minor' => max(0, $accountingProfitMinor - $prior->minorAmount()),
        ];

        return new ToolCalculationResult(
            toolSlug: 'simulador-distribuicao-lucros-balanco',
            schemaVersion: '1.1.0',
            summary: [
                new ToolCalculationSummaryItem('with_balance', 'Estimativa com balanço', Money::fromMinor($withBalance)->formatPtBr()),
                new ToolCalculationSummaryItem('without_balance', 'Estimativa sem balanço', Money::fromMinor($withoutBalance)->formatPtBr()),
                new ToolCalculationSummaryItem('difference', 'Diferença entre cenários', Money::fromMinor(abs($advantage))->formatPtBr()),
                new ToolCalculationSummaryItem('best', 'Maior capacidade estimada', $advantage >= 0 ? 'Com balanço' : 'Sem balanço'),
            ],
            details: [
                'with_balance_minor' => $withBalance,
                'without_balance_minor' => $withoutBalance,
                'reference_gross_minor' => $withoutBalanceGross,
                'difference_minor' => $advantage,
                'prior_distributions_minor' => $prior->minorAmount(),
                'profit_margin_percent' => number_format($profitBasisPoints / 100, 2, '.', ''),
                'tax_burden_percent' => number_format($taxBasisPoints / 100, 2, '.', ''),
                'bookkeeping' => $bookkeeping,
                'planning' => [
                    'months' => $input->planningMonths,
                    'monthly_growth_rate' => $growth->toDecimalString(),
                    'monthly_pro_labore_minor' => $proLabore->minorAmount(),
                    'rows' => $plan,
                    'annual_rows' => array_values($annualPlan),
                    'accumulated_with_balance_minor' => $cumulativeWithBalance,
                    'accumulated_without_balance_minor' => $cumulativeWithoutBalance,
                    'total_pro_labore_minor' => $cumulativeProLabore,
                ],
            ],
            warnings: [
                new ToolCalculationWarning(
                    'accounting_estimate',
                    $input->simulateBookkeeping
                        ? 'O cenário com balanço usa uma escrituração gerencial simplificada a partir de receita, tributos, pró-labore e despesas informados. Não substitui escrituração contábil formal nem demonstrações assinadas.'
                        : 'O cenário com balanço usa o lucro contábil informado. Ative a simulação de escrituração para estimar esse lucro a partir dos componentes do período.',
                    ToolCalculationWarningLevel::Info,
                ),
                new ToolCalculationWarning(
                    'tax_treatment',
                    'A capacidade contábil estimada não determina, por si só, o tratamento tributário da distribuição, retenções, limites por beneficiário ou obrigações. Confirme a legislação e a escrituração do caso concreto.',
                    ToolCalculationWarningLevel::Info,
                ),
            ],
            calculationMemory: new CalculationMemory('1.1.0', [
                new CalculationMemoryStep(
                    'bookkeeping',
                    'Lucro contábil usado',
                    $input->simulateBookkeeping
                        ? 'receita − tributos − pró-labore anual − despesas operacionais − outras despesas'
                        : 'lucro contábil informado',
                    $bookkeeping,
                    $accountingProfitMinor,
                ),
                new CalculationMemoryStep(
                    'with',
                    'Com balanço',
                    'máximo(0; lucro contábil usado − distribuições anteriores)',
                    ['profit_minor' => $accountingProfitMinor, 'prior_minor' => $prior->minorAmount()],
                    $withBalance,
                ),
                new CalculationMemoryStep(
                    'without',
                    'Sem balanço',
                    'máximo(0; receita × percentual de referência − tributos informados − distribuições anteriores)',
                    ['revenue_minor' => $revenue->minorAmount(), 'reference' => $referenceMargin->toDecimalString(), 'taxes_minor' => $taxes->minorAmount(), 'prior_minor' => $prior->minorAmount()],
                    $withoutBalance,
                    'Arredondamento monetário em centavos.',
                ),
            ], assumptions: [
                'A simulação de escrituração é gerencial e usa somente os valores informados no formulário.',
                'O percentual sem balanço é um parâmetro informado pelo usuário e não uma classificação automática da atividade.',
            ], isEstimate: true),
        );
    }
}
