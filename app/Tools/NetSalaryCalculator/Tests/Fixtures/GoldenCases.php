<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'INSS: Portaria Interministerial MPS/MF nº 13/2026; IRRF: Receita Federal, Tributação de 2026 e Lei nº 15.270/2025; casos protegidos por CalculatorTest.';

        return new GoldenCaseSuite(
            toolSlug: 'calculadora-salario-liquido',
            cases: [
                new GoldenCase(
                    identifier: 'typical',
                    title: 'Salário de R$ 5.000 com redução integral do IRRF',
                    kind: GoldenCaseKind::Typical,
                    input: ['competence' => '2026-01', 'base_salary' => '5000.00'],
                    expected: ['inss' => 'R$ 501,51', 'irrf' => 'R$ 0,00', 'net' => 'R$ 4.498,49'],
                    reference: $reference,
                    normativeRuleVersion: 'net_salary.employee_social_security:2026.1.0|tax.irrf.monthly:2026.1.0',
                    roundingPolicy: 'Progressividade sem float; arredondamento HalfUp em centavos.',
                ),
                new GoldenCase(
                    identifier: 'boundary',
                    title: 'Teto previdenciário de 2026 limita a base do INSS',
                    kind: GoldenCaseKind::Boundary,
                    input: ['competence' => '2026-01', 'base_salary' => '10000.00'],
                    expected: ['social_security_base_minor' => 847555, 'inss' => 'R$ 988,09'],
                    reference: $reference,
                    normativeRuleVersion: 'net_salary.employee_social_security:2026.1.0',
                    roundingPolicy: 'Progressividade acumulada e arredondamento HalfUp.',
                ),
                new GoldenCase(
                    identifier: 'invalid-input',
                    title: 'Salário-base zero é rejeitado',
                    kind: GoldenCaseKind::InvalidInput,
                    input: ['base_salary' => '0.00'],
                    expected: ['outcome' => 'validation-error'],
                    reference: 'ExecuteToolRequest exige money_min:0.01 e o domínio exige salário-base positivo.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'rounding',
                    title: 'INSS progressivo preserva centavos sem float',
                    kind: GoldenCaseKind::Rounding,
                    input: ['competence' => '2026-01', 'base_salary' => '3036.00'],
                    expected: ['inss' => 'R$ 252,92'],
                    reference: $reference,
                    normativeRuleVersion: 'net_salary.employee_social_security:2026.1.0',
                    roundingPolicy: 'Soma das faixas em inteiros escalados e HalfUp no total acumulado.',
                ),
                new GoldenCase(
                    identifier: 'non-applicable',
                    title: 'Múltiplos vínculos ficam fora do escopo',
                    kind: GoldenCaseKind::NonApplicable,
                    input: ['scenario' => 'multiple-employment-links'],
                    expected: ['outcome' => 'explicitly-out-of-scope'],
                    reference: 'README do módulo e Manual de Orientação do eSocial documentam tratamento específico para múltiplos vínculos.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'normative-transition',
                    title: 'Competência seleciona regras versionadas',
                    kind: GoldenCaseKind::NormativeTransition,
                    input: ['competence' => '2026-12'],
                    expected: ['rules' => ['net_salary.employee_social_security:2026.1.0', 'tax.irrf.monthly:2026.1.0']],
                    reference: $reference,
                    normativeRuleVersion: '2026.1.0',
                ),
                new GoldenCase(
                    identifier: 'regression',
                    title: 'Salário de R$ 10.000 mantém resultado conhecido',
                    kind: GoldenCaseKind::Regression,
                    input: ['competence' => '2026-01', 'base_salary' => '10000.00'],
                    expected: ['inss' => 'R$ 988,09', 'irrf' => 'R$ 1.569,55', 'net' => 'R$ 7.442,36'],
                    reference: $reference,
                    normativeRuleVersion: '2026.1.0',
                    roundingPolicy: 'Valores em centavos; percentuais via Percentage; HalfUp.',
                ),
            ],
        );
    }
}
