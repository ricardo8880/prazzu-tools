<?php

declare(strict_types=1);

namespace App\Tools\DifalIcmsCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'EC 87/2015; LC 190/2022 e LC 87/1996; Resoluções do Senado nº 22/1989 e nº 13/2012; documentação normativa do módulo e CalculatorTest.';

        return new GoldenCaseSuite(
            toolSlug: 'calculadora-difal-icms',
            cases: [
                new GoldenCase(
                    identifier: 'typical',
                    title: 'Operação SP para BA com alíquota interestadual de 7%',
                    kind: GoldenCaseKind::Typical,
                    input: ['competence' => '2026-07', 'base' => '1000.00', 'origin_uf' => 'SP', 'destination_uf' => 'BA', 'internal_rate' => '18', 'fcp_rate' => '2'],
                    expected: ['interstate_rate' => '7%', 'difal' => 'R$ 110,00', 'fcp' => 'R$ 20,00', 'total' => 'R$ 130,00'],
                    reference: $reference,
                    normativeRuleVersion: 'difal.icms.interstate:2026.1.0',
                    roundingPolicy: 'Money em centavos e Percentage escalada; HalfUp.',
                ),
                new GoldenCase(
                    identifier: 'boundary',
                    title: 'Alíquota interna igual à interestadual produz DIFAL zero',
                    kind: GoldenCaseKind::Boundary,
                    input: ['competence' => '2026-07', 'base' => '1000.00', 'origin_uf' => 'SP', 'destination_uf' => 'MG', 'internal_rate' => '12', 'fcp_rate' => '0'],
                    expected: ['interstate_rate' => '12%', 'difal' => 'R$ 0,00', 'total' => 'R$ 0,00'],
                    reference: $reference,
                    normativeRuleVersion: 'difal.icms.interstate:2026.1.0',
                ),
                new GoldenCase(
                    identifier: 'invalid-input',
                    title: 'Origem e destino iguais são rejeitados',
                    kind: GoldenCaseKind::InvalidInput,
                    input: ['origin_uf' => 'SP', 'destination_uf' => 'SP'],
                    expected: ['outcome' => 'validation-error'],
                    reference: 'ExecuteToolRequest usa different:origin_uf e o domínio também rejeita DIFAL para UFs iguais.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'rounding',
                    title: 'Base com centavos preserva arredondamento fiscal',
                    kind: GoldenCaseKind::Rounding,
                    input: ['competence' => '2026-07', 'base' => '1234.56', 'origin_uf' => 'SP', 'destination_uf' => 'BA', 'internal_rate' => '18', 'fcp_rate' => '2'],
                    expected: ['difal' => 'R$ 135,80', 'fcp' => 'R$ 24,69', 'total' => 'R$ 160,49'],
                    reference: $reference,
                    normativeRuleVersion: 'difal.icms.interstate:2026.1.0',
                    roundingPolicy: 'Operações em centavos e percentuais escalados; IntegerRounding HalfUp na base dupla.',
                ),
                new GoldenCase(
                    identifier: 'non-applicable',
                    title: 'Alíquota interna, FCP e benefícios não são inferidos por UF isoladamente',
                    kind: GoldenCaseKind::NonApplicable,
                    input: ['scenario' => 'automatic-internal-rate-from-state-only'],
                    expected: ['outcome' => 'explicitly-not-inferred'],
                    reference: 'README e NORMATIVE_RULES do módulo exigem confirmação da legislação estadual, mercadoria/serviço, benefício e enquadramento.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'normative-transition',
                    title: 'Competência seleciona a regra interestadual versionada',
                    kind: GoldenCaseKind::NormativeTransition,
                    input: ['competence' => '2026-12'],
                    expected: ['rules' => ['difal.icms.interstate:2026.1.0']],
                    reference: $reference,
                    normativeRuleVersion: '2026.1.0',
                ),
                new GoldenCase(
                    identifier: 'regression',
                    title: 'Mercadoria enquadrada na Resolução 13 preserva alíquota de 4%',
                    kind: GoldenCaseKind::Regression,
                    input: ['competence' => '2026-07', 'base' => '1000.00', 'origin_uf' => 'SP', 'destination_uf' => 'MG', 'imported' => true, 'internal_rate' => '18'],
                    expected: ['interstate_rate' => '4%', 'difal' => 'R$ 140,00'],
                    reference: $reference,
                    normativeRuleVersion: 'difal.icms.interstate:2026.1.0',
                    roundingPolicy: 'Money em centavos e Percentage escalada; HalfUp.',
                ),
            ],
        );
    }
}
