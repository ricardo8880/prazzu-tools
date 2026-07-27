<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'CLT, arts. 59 e 73; Lei nº 605/1949; documentação normativa do módulo e casos protegidos por CalculatorTest.';

        return new GoldenCaseSuite(
            toolSlug: 'calculadora-hora-extra',
            cases: [
                new GoldenCase(
                    identifier: 'typical',
                    title: 'Dez horas extras a 50% sobre salário de R$ 2.200',
                    kind: GoldenCaseKind::Typical,
                    input: ['competence' => '2026-07', 'base_salary' => '2200.00', 'monthly_hours' => 220, 'overtime_50_hours' => '10'],
                    expected: ['hourly' => 'R$ 10,00', 'overtime' => 'R$ 150,00'],
                    reference: $reference,
                    normativeRuleVersion: 'overtime.labor_compensation:2026.1.0',
                    roundingPolicy: 'Valores em centavos e horas em milésimos; HalfUp nas divisões.',
                ),
                new GoldenCase(
                    identifier: 'boundary',
                    title: 'Mês sem horas extraordinárias mantém total variável zero',
                    kind: GoldenCaseKind::Boundary,
                    input: ['competence' => '2026-07', 'base_salary' => '2200.00', 'monthly_hours' => 220, 'overtime_50_hours' => '0'],
                    expected: ['hourly' => 'R$ 10,00', 'total_variable' => 'R$ 0,00'],
                    reference: 'Contrato do domínio: quantidade zero de horas é um limite válido e não cria remuneração variável.',
                    normativeRuleVersion: 'overtime.labor_compensation:2026.1.0',
                ),
                new GoldenCase(
                    identifier: 'invalid-input',
                    title: 'Adicional personalizado inferior a 50% é rejeitado',
                    kind: GoldenCaseKind::InvalidInput,
                    input: ['custom_premium' => '49.99'],
                    expected: ['outcome' => 'validation-error'],
                    reference: 'ExecuteToolRequest exige custom_premium mínimo de 50%, em linha com o mínimo constitucional/CLT aplicável ao caso comum coberto.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'rounding',
                    title: 'Hora fracionada preserva centavos sem ponto flutuante',
                    kind: GoldenCaseKind::Rounding,
                    input: ['competence' => '2026-07', 'base_salary' => '1000.00', 'monthly_hours' => 220, 'overtime_50_hours' => '1.001'],
                    expected: ['hourly' => 'R$ 4,55', 'overtime' => 'R$ 6,83'],
                    reference: $reference,
                    normativeRuleVersion: 'overtime.labor_compensation:2026.1.0',
                    roundingPolicy: 'Money em centavos, horas em milésimos e IntegerRounding HalfUp.',
                ),
                new GoldenCase(
                    identifier: 'non-applicable',
                    title: 'Banco de horas e categorias especiais ficam fora do escopo automático',
                    kind: GoldenCaseKind::NonApplicable,
                    input: ['scenario' => 'collective-agreement-or-special-category'],
                    expected: ['outcome' => 'explicitly-out-of-scope'],
                    reference: 'README e memória de cálculo do módulo exigem análise prévia de CCT/ACT, banco de horas, 12x36 e categorias especiais.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'normative-transition',
                    title: 'Competência seleciona a regra trabalhista versionada',
                    kind: GoldenCaseKind::NormativeTransition,
                    input: ['competence' => '2026-12'],
                    expected: ['rules' => ['overtime.labor_compensation:2026.1.0']],
                    reference: $reference,
                    normativeRuleVersion: '2026.1.0',
                ),
                new GoldenCase(
                    identifier: 'regression',
                    title: 'Hora noturna reduzida e DSR mantêm cenário conhecido',
                    kind: GoldenCaseKind::Regression,
                    input: [
                        'competence' => '2026-07',
                        'base_salary' => '2200.00',
                        'monthly_hours' => 220,
                        'overtime_50_hours' => '10',
                        'night_clock_hours' => '7',
                        'working_days' => 22,
                        'rest_days' => 4,
                        'include_dsr' => true,
                        'include_reflexes' => true,
                    ],
                    expected: ['night_premium' => 'R$ 16,00', 'dsr' => 'R$ 30,18', 'total_variable' => 'R$ 196,18'],
                    reference: $reference,
                    normativeRuleVersion: 'overtime.labor_compensation:2026.1.0',
                    roundingPolicy: 'Hora noturna convertida por 3600/3150; DSR e reflexos com HalfUp.',
                ),
            ],
        );
    }
}
