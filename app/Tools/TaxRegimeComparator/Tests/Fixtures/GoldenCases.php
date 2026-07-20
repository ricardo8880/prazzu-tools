<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        return new GoldenCaseSuite(
            toolSlug: 'comparador-tributario',
            cases: [
                self::case(
                    identifier: 'typical.services-sp',
                    title: 'Prestação de serviços em cenário mensal típico',
                    kind: GoldenCaseKind::Typical,
                    input: ['monthly_revenue' => '100000.00', 'state' => 'SP', 'activity' => 'services'],
                    expected: ['comparable_regime_count' => 3, 'ranking_generated' => true],
                    reference: 'Baseline técnico aprovado do Comparador Tributário — cenário típico v1.',
                ),
                self::case(
                    identifier: 'boundary.zero-payroll',
                    title: 'Cenário de fronteira sem folha acumulada',
                    kind: GoldenCaseKind::Boundary,
                    input: ['monthly_revenue' => '100000.00', 'payroll_last_twelve_months' => '0.00'],
                    expected: ['comparison_completed' => true, 'warnings_present' => true],
                    reference: 'Baseline técnico aprovado do Comparador Tributário — fronteira de folha v1.',
                ),
                self::case(
                    identifier: 'invalid-input.negative-revenue',
                    title: 'Receita mensal negativa é rejeitada',
                    kind: GoldenCaseKind::InvalidInput,
                    input: ['monthly_revenue' => '-1.00'],
                    expected: ['validation_error' => 'monthly_revenue'],
                    reference: 'Contrato de validação do Comparador Tributário — entradas monetárias não negativas.',
                ),
                self::case(
                    identifier: 'rounding.tax-total',
                    title: 'Totais tributários são arredondados em centavos',
                    kind: GoldenCaseKind::Rounding,
                    input: ['monthly_revenue' => '100000.01', 'indirect_tax_rate' => '5.005'],
                    expected: ['money_scale' => 2, 'rounding_policy' => 'half_up'],
                    reference: 'Política monetária do Core — valores finais expressos em centavos.',
                    roundingPolicy: 'Arredondamento half-up para duas casas decimais nos totais monetários apresentados.',
                ),
                self::case(
                    identifier: 'non-applicable.missing-provider',
                    title: 'Regime sem estimador compatível não participa do ranking',
                    kind: GoldenCaseKind::NonApplicable,
                    input: ['available_provider_count' => 2],
                    expected: ['comparable_regime_count' => 2, 'unavailable_regime_reported' => true],
                    reference: 'Contrato TaxEstimateProvider do Core — estimativas indisponíveis não são comparáveis.',
                ),
                self::case(
                    identifier: 'normative-transition.reference-date',
                    title: 'Data de referência seleciona a regra tributária vigente',
                    kind: GoldenCaseKind::NormativeTransition,
                    input: ['reference_date' => '2026-01-01', 'monthly_revenue' => '100000.00'],
                    expected: ['rule_version_present' => true],
                    reference: 'Contrato de versionamento normativo do Core — vigência determinada pela data de referência.',
                    normativeRuleVersion: 'tax-regime-comparator-baseline-v1',
                ),
                self::case(
                    identifier: 'regression.ranking-order',
                    title: 'Ranking permanece ordenado pelo menor tributo mensal',
                    kind: GoldenCaseKind::Regression,
                    input: ['estimated_monthly_taxes' => ['10000.00', '8000.00', '12000.00']],
                    expected: ['ranking' => ['8000.00', '10000.00', '12000.00']],
                    reference: 'Regressão aprovada do Comparador Tributário — ordenação crescente das estimativas v1.',
                ),
            ],
        );
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed> $expected
     */
    private static function case(
        string $identifier,
        string $title,
        GoldenCaseKind $kind,
        array $input,
        array $expected,
        string $reference,
        ?string $normativeRuleVersion = null,
        ?string $roundingPolicy = null,
    ): GoldenCase {
        return new GoldenCase(
            identifier: $identifier,
            title: $title,
            kind: $kind,
            input: $input,
            expected: $expected,
            reference: $reference,
            normativeRuleVersion: $normativeRuleVersion,
            roundingPolicy: $roundingPolicy,
        );
    }
}
