<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $calculatorReference = 'AccountingFeesCalculatorTest protege o cenário de R$ 100.000, 5 empregados, 2 sócios, comércio, Simples Nacional e complexidade média.';
        $adjustmentReference = 'FeeAdjustmentCalculatorTest protege reajuste positivo, negativo, arredondamento HalfUp e rejeição de valor não positivo.';

        return new GoldenCaseSuite(
            toolSlug: 'calculadora-de-honorarios-contabeis',
            cases: [
                new GoldenCase(
                    identifier: 'typical',
                    title: 'Cenário comercial médio preserva honorário recomendado conhecido',
                    kind: GoldenCaseKind::Typical,
                    input: ['monthly_revenue' => '100000.00', 'employees' => 5, 'partners' => 2, 'monthly_invoices' => 120, 'monthly_bank_transactions' => 250, 'tax_regime' => 'simples_nacional', 'segment' => 'commerce', 'complexity' => 'medium'],
                    expected: ['minimum_fee' => 'R$ 1.678,43', 'recommended_fee' => 'R$ 1.930,19', 'complexity_score' => 46],
                    reference: $calculatorReference,
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Valores monetários em centavos, sem float.',
                ),
                new GoldenCase(
                    identifier: 'boundary',
                    title: 'Ao menos um sócio ou titular é obrigatório',
                    kind: GoldenCaseKind::Boundary,
                    input: ['monthly_revenue' => '10000.00', 'employees' => 0, 'partners' => 0],
                    expected: ['outcome' => 'domain-error', 'message' => 'Informe pelo menos um sócio ou titular.'],
                    reference: $calculatorReference,
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'invalid-input',
                    title: 'Reajuste rejeita honorário atual igual a zero',
                    kind: GoldenCaseKind::InvalidInput,
                    input: ['current_value' => '0.00', 'percentage' => '5'],
                    expected: ['outcome' => 'domain-error'],
                    reference: $adjustmentReference,
                    normativeRuleVersion: 'fee-adjustment-v1',
                ),
                new GoldenCase(
                    identifier: 'rounding',
                    title: 'Reajuste de um centavo em cinquenta por cento usa HalfUp',
                    kind: GoldenCaseKind::Rounding,
                    input: ['current_value_minor' => 1, 'percentage' => '50'],
                    expected: ['difference_minor' => 1, 'adjusted_value_minor' => 2],
                    reference: $adjustmentReference,
                    normativeRuleVersion: 'fee-adjustment-v1',
                    roundingPolicy: 'HalfUp em centavos.',
                ),
                new GoldenCase(
                    identifier: 'non-applicable',
                    title: 'Índices de reajuste não são consultados automaticamente',
                    kind: GoldenCaseKind::NonApplicable,
                    input: ['scenario' => 'automatic-official-index-lookup'],
                    expected: ['outcome' => 'explicitly-out-of-scope'],
                    reference: 'README do módulo exige que o usuário informe a taxa oficial aplicável e registre sua origem.',
                    normativeRuleVersion: '1.2.0',
                ),
            ],
        );
    }
}
