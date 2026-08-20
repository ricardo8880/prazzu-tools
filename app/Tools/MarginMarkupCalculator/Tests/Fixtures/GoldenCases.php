<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'MarginMarkupCalculatorTest protege fórmula, composição de custos, rejeições de domínio e memória de cálculo da regra 2.1.0.';

        return new GoldenCaseSuite(
            toolSlug: 'calculadora-margem-markup',
            cases: [
                new GoldenCase(
                    identifier: 'typical',
                    title: 'Custo total de R$ 120 com percentuais de venda produz preço de R$ 200',
                    kind: GoldenCaseKind::Typical,
                    input: ['base_cost' => '100.00', 'additional_costs' => '10.00', 'freight_cost' => '5.00', 'packaging_cost' => '3.00', 'fixed_expenses' => '2.00', 'desired_margin' => '20', 'taxes' => '10', 'commission' => '5', 'card_fees' => '2', 'marketplace_fees' => '3'],
                    expected: ['total_cost' => 'R$ 120,00', 'sale_price' => 'R$ 200,00', 'net_profit' => 'R$ 40,00', 'markup_multiplier' => '1,6667'],
                    reference: $reference,
                    normativeRuleVersion: '2.1.0',
                    roundingPolicy: 'Divisões monetárias usam HalfUp; valores são preservados em centavos.',
                ),
                new GoldenCase(
                    identifier: 'boundary',
                    title: 'Soma dos percentuais igual a cem por cento não possui denominador válido',
                    kind: GoldenCaseKind::Boundary,
                    input: ['base_cost' => '100.00', 'desired_margin' => '50', 'taxes' => '30', 'commission' => '20'],
                    expected: ['outcome' => 'domain-error', 'message' => 'A soma da margem, impostos, comissão e taxas deve ser menor que 100%.'],
                    reference: $reference,
                    normativeRuleVersion: '2.1.0',
                ),
                new GoldenCase(
                    identifier: 'invalid-input',
                    title: 'Custo total zero é rejeitado',
                    kind: GoldenCaseKind::InvalidInput,
                    input: ['base_cost' => '0.00', 'desired_margin' => '25'],
                    expected: ['outcome' => 'domain-error', 'message' => 'O custo total deve ser maior que zero.'],
                    reference: $reference,
                    normativeRuleVersion: '2.1.0',
                ),
                new GoldenCase(
                    identifier: 'rounding',
                    title: 'Preço e markup são derivados sem float no domínio',
                    kind: GoldenCaseKind::Rounding,
                    input: ['total_cost_minor' => 12000, 'sale_price_minor' => 20000],
                    expected: ['markup' => '66.666667', 'markup_multiplier' => '1,6667'],
                    reference: $reference,
                    normativeRuleVersion: '2.1.0',
                    roundingPolicy: 'Percentuais usam Percentage e divisões monetárias usam IntegerRounding/HalfUp.',
                ),
                new GoldenCase(
                    identifier: 'non-applicable',
                    title: 'Custos fixos empresariais não são rateados automaticamente',
                    kind: GoldenCaseKind::NonApplicable,
                    input: ['scenario' => 'automatic-fixed-cost-allocation'],
                    expected: ['outcome' => 'explicitly-out-of-scope'],
                    reference: 'README do módulo exige que custos fixos sejam previamente rateados por unidade ou venda.',
                    normativeRuleVersion: '2.1.0',
                ),
            ],
        );
    }
}
