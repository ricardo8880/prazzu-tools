<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: replace with an official source, reviewed calculation, or approved case.';

    public static function suite(): GoldenCaseSuite
    {
        return new GoldenCaseSuite(
            toolSlug: 'pay-stub-generator',
            cases: [
                new GoldenCase(
                    identifier: 'typical',
                    title: 'Fluxo principal validado',
                    kind: GoldenCaseKind::Typical,
                    input: ['scenario' => 'valid-typical-input'],
                    expected: ['outcome' => 'calculation-or-document-completed'],
                    reference: 'Module CalculatorTest and calculation details for version 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Monetary values use integer cents, without floats.',
                ),
                new GoldenCase(
                    identifier: 'boundary',
                    title: 'Domain boundary validated',
                    kind: GoldenCaseKind::Boundary,
                    input: ['scenario' => 'valid-boundary-input'],
                    expected: ['outcome' => 'boundary-handled-without-loss'],
                    reference: 'Validation rules and module CalculatorTest for version 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Monetary values use integer cents, without floats.',
                ),
                new GoldenCase(
                    identifier: 'invalid-input',
                    title: 'Invalid input rejected',
                    kind: GoldenCaseKind::InvalidInput,
                    input: ['scenario' => 'invalid-domain-input'],
                    expected: ['outcome' => 'validation-error'],
                    reference: 'FormRequest and module domain invariants for version 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'rounding',
                    title: 'Stable monetary rounding',
                    kind: GoldenCaseKind::Rounding,
                    input: ['scenario' => 'fractional-monetary-input'],
                    expected: ['outcome' => 'integer-cent-result'],
                    reference: 'Core Money policy and module CalculatorTest for version 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Monetary values use integer cents, without floats.',
                ),
                new GoldenCase(
                    identifier: 'non-applicable',
                    title: 'Non-applicable scenario identified',
                    kind: GoldenCaseKind::NonApplicable,
                    input: ['scenario' => 'non-applicable-input'],
                    expected: ['outcome' => 'explicit-non-applicable-result'],
                    reference: 'Module domain invariants and warnings for version 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'normative-transition',
                    title: 'Rule version identified',
                    kind: GoldenCaseKind::NormativeTransition,
                    input: ['scenario' => 'rule-version-transition'],
                    expected: ['outcome' => 'versioned-rule-result'],
                    reference: 'Calculation details and rule metadata for module version 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'regression',
                    title: 'Primary result protected against regression',
                    kind: GoldenCaseKind::Regression,
                    input: ['scenario' => 'known-regression-input'],
                    expected: ['outcome' => 'stable-versioned-result'],
                    reference: 'Module CalculatorTest approved for version 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Monetary values use integer cents, without floats.',
                ),
            ],
        );
    }
}
