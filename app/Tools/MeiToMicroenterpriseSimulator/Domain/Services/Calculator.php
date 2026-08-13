<?php

declare(strict_types=1);

namespace App\Tools\MeiToMicroenterpriseSimulator\Domain\Services;

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
use App\Tools\MeiToMicroenterpriseSimulator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public const CURRENT_REFERENCE_YEAR = 2026;

    public const CURRENT_MEI_LIMIT_MINOR = 8_100_000;

    /** @var array<int, int> */
    private const MEI_LIMITS_MINOR = [
        2026 => 8_100_000,
        2027 => 11_000_000,
        2028 => 14_000_000,
    ];

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com o simulador MEI para Microempresa.');
        }

        $current = Money::fromDecimal($input->currentAnnualRevenue);
        $projected = Money::fromDecimal($input->projectedAnnualRevenue);
        if ($current->minorAmount() < 0 || $projected->minorAmount() <= 0) {
            throw new InvalidArgumentException('Informe faturamentos válidos.');
        }

        $limit = self::CURRENT_MEI_LIMIT_MINOR;
        $twentyPercentLimit = IntegerRounding::divide($limit * 120, 100);
        $excess = max(0, $projected->minorAmount() - $limit);
        $headroom = max(0, $limit - $projected->minorAmount());
        $projectedPercentOfLimit = IntegerRounding::divide($projected->minorAmount() * 10_000, $limit);

        [$band, $bandLabel, $impactText] = match (true) {
            $projected->minorAmount() <= $limit => [
                'within_limit',
                'Dentro do limite do MEI',
                'A projeção permanece dentro do teto anual de referência. Ainda é necessário confirmar os demais requisitos para permanência no SIMEI.',
            ],
            $projected->minorAmount() <= $twentyPercentLimit => [
                'excess_up_to_20',
                'Acima do limite em até 20%',
                'Há excesso projetado de receita. Fora do ano de início de atividade, o desenquadramento por excesso de até 20% produz efeitos, em regra, no ano-calendário seguinte.',
            ],
            default => [
                'excess_over_20',
                'Acima do limite em mais de 20%',
                'O excesso projetado supera 20% do teto. Fora do ano de início de atividade, essa faixa pode produzir desenquadramento retroativo ao início do próprio ano-calendário.',
            ],
        };

        $taxRate = Percentage::fromString($input->meEffectiveTaxRate);
        $growthRate = Percentage::fromString($input->annualGrowthRate);
        $targetBurden = Percentage::fromString($input->targetFixedCostBurden);
        $accounting = Money::fromDecimal($input->monthlyAccountingCost);
        $other = Money::fromDecimal($input->monthlyOtherCost);
        $meiMonthlyCost = Money::fromDecimal($input->monthlyMeiCost);
        if ($taxRate->millionthsOfPercent() < 0 || $growthRate->millionthsOfPercent() < 0 || $targetBurden->millionthsOfPercent() <= 0) {
            throw new InvalidArgumentException('As taxas da projeção devem ser válidas.');
        }
        if ($accounting->minorAmount() < 0 || $other->minorAmount() < 0 || $meiMonthlyCost->minorAmount() < 0 || $input->projectionYears < 1 || $input->projectionYears > 10) {
            throw new InvalidArgumentException('Custos ou período de projeção inválidos.');
        }

        $annualFixedCosts = ($accounting->minorAmount() + $other->minorAmount()) * 12;
        $annualMeiCost = $meiMonthlyCost->minorAmount() * 12;
        $incrementalFixedCost = max(0, $annualFixedCosts - $annualMeiCost);
        $migrationLessWeightRevenue = $this->migrationLessWeightRevenue($incrementalFixedCost, $taxRate, $targetBurden);
        $projection = [];
        $revenue = $projected->minorAmount();

        for ($i = 0; $i < $input->projectionYears; $i++) {
            $year = self::CURRENT_REFERENCE_YEAR + $i;
            if ($i > 0) {
                $growth = Money::fromMinor($revenue)->percentage($growthRate)->minorAmount();
                $revenue += $growth;
            }
            $estimatedTaxes = Money::fromMinor($revenue)->percentage($taxRate)->minorAmount();
            $totalBusinessCost = $estimatedTaxes + $annualFixedCosts;
            $incrementalMigrationCost = max(0, $totalBusinessCost - $annualMeiCost);
            $incrementalBurdenBasisPoints = $revenue > 0 ? IntegerRounding::divide($incrementalMigrationCost * 10_000, $revenue) : 0;
            $netAfterBusinessCosts = max(0, $revenue - $totalBusinessCost);
            $burdenBasisPoints = $revenue > 0 ? IntegerRounding::divide($totalBusinessCost * 10_000, $revenue) : 0;
            $yearLimit = $this->meiLimitForYear($year);

            $projection[] = [
                'year' => $year,
                'revenue_minor' => $revenue,
                'mei_limit_minor' => $yearLimit,
                'estimated_taxes_minor' => $estimatedTaxes,
                'fixed_costs_minor' => $annualFixedCosts,
                'total_business_cost_minor' => $totalBusinessCost,
                'mei_reference_cost_minor' => $annualMeiCost,
                'incremental_migration_cost_minor' => $incrementalMigrationCost,
                'incremental_migration_burden_percent' => number_format($incrementalBurdenBasisPoints / 100, 2, '.', ''),
                'net_after_business_costs_minor' => $netAfterBusinessCosts,
                'cost_burden_percent' => number_format($burdenBasisPoints / 100, 2, '.', ''),
            ];
        }

        return new ToolCalculationResult(
            toolSlug: 'simulador-mei-microempresa',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('projected_revenue', 'Faturamento anual projetado', $projected->formatPtBr()),
                new ToolCalculationSummaryItem('mei_limit', 'Teto MEI de referência — 2026', Money::fromMinor($limit)->formatPtBr()),
                new ToolCalculationSummaryItem('status', 'Impacto estimado', $bandLabel),
                new ToolCalculationSummaryItem('difference', $excess > 0 ? 'Excesso projetado' : 'Folga até o teto', Money::fromMinor($excess > 0 ? $excess : $headroom)->formatPtBr()),
            ],
            details: [
                'reference_year' => self::CURRENT_REFERENCE_YEAR,
                'current_revenue_minor' => $current->minorAmount(),
                'projected_revenue_minor' => $projected->minorAmount(),
                'mei_limit_minor' => $limit,
                'twenty_percent_limit_minor' => $twentyPercentLimit,
                'excess_minor' => $excess,
                'headroom_minor' => $headroom,
                'projected_percent_of_limit' => number_format($projectedPercentOfLimit / 100, 2, '.', ''),
                'band' => $band,
                'band_label' => $bandLabel,
                'impact_text' => $impactText,
                'plus' => [
                    'me_effective_tax_rate' => $taxRate->toDecimalString(),
                    'monthly_accounting_cost_minor' => $accounting->minorAmount(),
                    'monthly_other_cost_minor' => $other->minorAmount(),
                    'annual_fixed_costs_minor' => $annualFixedCosts,
                    'monthly_mei_cost_minor' => $meiMonthlyCost->minorAmount(),
                    'annual_mei_cost_minor' => $annualMeiCost,
                    'incremental_fixed_cost_minor' => $incrementalFixedCost,
                    'annual_growth_rate' => $growthRate->toDecimalString(),
                    'projection_years' => $input->projectionYears,
                    'target_fixed_cost_burden' => $targetBurden->toDecimalString(),
                    'migration_less_weight_revenue_minor' => $migrationLessWeightRevenue,
                    'migration_point_reached' => $migrationLessWeightRevenue !== null,
                    'projection' => $projection,
                ],
            ],
            warnings: [
                new ToolCalculationWarning(
                    'estimate_only',
                    'Esta é uma simulação de planejamento. Ela não determina automaticamente o regime tributário, o anexo do Simples Nacional, a alíquota efetiva nem a data jurídica do desenquadramento.',
                    ToolCalculationWarningLevel::Info,
                ),
                new ToolCalculationWarning(
                    'normative_reference',
                    'Referência vigente usada no cenário Essencial: teto anual do MEI de R$ 81.000 em 2026. Para 2027 e 2028, a projeção Plus considera os limites divulgados oficialmente de R$ 110.000 e R$ 140.000. Confirme a legislação aplicável antes de decidir.',
                    ToolCalculationWarningLevel::Info,
                ),
                new ToolCalculationWarning(
                    'plus_parameters',
                    'No Plus, impostos e custos são estimados a partir da alíquota efetiva e dos custos mensais informados por você; a ferramenta não presume CNAE, anexo, Fator R, ICMS/ISS fora do DAS ou obrigações acessórias específicas.',
                    ToolCalculationWarningLevel::Info,
                ),
            ],
            calculationMemory: new CalculationMemory('1.0.0', [
                new CalculationMemoryStep(
                    'mei_limit_comparison',
                    'Comparação com o teto do MEI',
                    'faturamento anual projetado − teto anual do MEI',
                    ['projected_revenue_minor' => $projected->minorAmount(), 'mei_limit_minor' => $limit],
                    $projected->minorAmount() - $limit,
                    'Valores monetários são calculados em centavos.',
                ),
                new CalculationMemoryStep(
                    'twenty_percent_band',
                    'Faixa de excesso de 20%',
                    'teto anual × 120%',
                    ['mei_limit_minor' => $limit],
                    $twentyPercentLimit,
                    'A faixa auxilia a leitura do impacto potencial do excesso; a data efetiva depende do caso concreto.',
                ),
                new CalculationMemoryStep(
                    'estimated_me_cost',
                    'Custo empresarial anual estimado — Plus',
                    '(faturamento × alíquota efetiva informada) + 12 × custos mensais informados',
                    ['tax_rate' => $taxRate->toDecimalString(), 'annual_fixed_costs_minor' => $annualFixedCosts],
                    $projection[0]['total_business_cost_minor'],
                    'A alíquota efetiva é um parâmetro do usuário, não uma classificação fiscal automática.',
                ),
                new CalculationMemoryStep(
                    'migration_less_weight',
                    'Ponto em que a migração pesa menos — Plus',
                    '(custos fixos anuais da ME − custo anual atual do MEI) ÷ (percentual-alvo − alíquota efetiva da ME)',
                    ['annual_fixed_costs_minor' => $annualFixedCosts, 'annual_mei_cost_minor' => $annualMeiCost, 'tax_rate' => $taxRate->toDecimalString(), 'target_burden' => $targetBurden->toDecimalString()],
                    $migrationLessWeightRevenue ?? 0,
                    'Compara o custo anual atual informado para o MEI com os impostos e custos projetados da ME. O percentual-alvo representa o peso adicional máximo desejado da migração sobre o faturamento.',
                ),
            ]),
        );
    }

    private function meiLimitForYear(int $year): int
    {
        if (isset(self::MEI_LIMITS_MINOR[$year])) {
            return self::MEI_LIMITS_MINOR[$year];
        }

        return self::MEI_LIMITS_MINOR[2028];
    }

    private function migrationLessWeightRevenue(int $incrementalFixedCostMinor, Percentage $taxRate, Percentage $targetBurden): ?int
    {
        if ($incrementalFixedCostMinor <= 0) {
            return 0;
        }

        $availableBurden = $targetBurden->millionthsOfPercent() - $taxRate->millionthsOfPercent();
        if ($availableBurden <= 0) {
            return null;
        }

        return IntegerRounding::divide($incrementalFixedCostMinor * 100_000_000, $availableBurden);
    }
}
