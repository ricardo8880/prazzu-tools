<?php

declare(strict_types=1);

namespace App\Tools\EmployerInssCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        return new GoldenCaseSuite(
            toolSlug: 'inss-patronal',
            cases: [
                new GoldenCase(
                    identifier: 'typical',
                    title: 'Fluxo principal validado',
                    kind: GoldenCaseKind::Typical,
                    input: ['scenario' => 'valid-typical-input'],
                    expected: ['outcome' => 'calculation-or-document-completed'],
                    reference: 'CalculatorTest do módulo e memória de cálculo da versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Valores monetários em centavos, sem float.',
                ),
                new GoldenCase(
                    identifier: 'boundary',
                    title: 'Limite do domínio validado',
                    kind: GoldenCaseKind::Boundary,
                    input: ['scenario' => 'valid-boundary-input'],
                    expected: ['outcome' => 'boundary-handled-without-loss'],
                    reference: 'Regras de validação e CalculatorTest do módulo na versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Valores monetários em centavos, sem float.',
                ),
                new GoldenCase(
                    identifier: 'invalid-input',
                    title: 'Entrada inválida rejeitada',
                    kind: GoldenCaseKind::InvalidInput,
                    input: ['scenario' => 'invalid-domain-input'],
                    expected: ['outcome' => 'validation-error'],
                    reference: 'FormRequest e invariantes do domínio do módulo na versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'rounding',
                    title: 'Arredondamento monetário estável',
                    kind: GoldenCaseKind::Rounding,
                    input: ['scenario' => 'fractional-monetary-input'],
                    expected: ['outcome' => 'integer-cent-result'],
                    reference: 'Política Money do Core e CalculatorTest do módulo na versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Valores monetários em centavos, sem float.',
                ),
                new GoldenCase(
                    identifier: 'non-applicable',
                    title: 'Cenário não aplicável identificado',
                    kind: GoldenCaseKind::NonApplicable,
                    input: ['scenario' => 'non-applicable-input'],
                    expected: ['outcome' => 'explicit-non-applicable-result'],
                    reference: 'Invariantes e avisos do domínio do módulo na versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'normative-transition',
                    title: 'Versão normativa identificada',
                    kind: GoldenCaseKind::NormativeTransition,
                    input: ['scenario' => 'rule-version-transition'],
                    expected: ['outcome' => 'versioned-rule-result'],
                    reference: 'Memória de cálculo e metadados normativos do módulo na versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'regression',
                    title: 'Resultado principal protegido contra regressão',
                    kind: GoldenCaseKind::Regression,
                    input: ['scenario' => 'known-regression-input'],
                    expected: ['outcome' => 'stable-versioned-result'],
                    reference: 'CalculatorTest do módulo aprovado para a versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Valores monetários em centavos, sem float.',
                ),
            ],
        );
    }
}
