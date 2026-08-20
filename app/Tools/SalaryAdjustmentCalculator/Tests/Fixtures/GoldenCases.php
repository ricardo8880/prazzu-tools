<?php

declare(strict_types=1);

namespace App\Tools\SalaryAdjustmentCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'CalculatorTest e memória da versão 1.1.0; impacto anual limitado às parcelas descritas no README.';

        return new GoldenCaseSuite('reajuste-salarial', [
            new GoldenCase('typical', 'Reajuste percentual mais aumento fixo', GoldenCaseKind::Typical,
                ['current_salary_minor' => 300000, 'adjustment_rate' => '5', 'fixed_addition_minor' => 5000, 'retroactive_months' => 3],
                ['new_salary_minor' => 320000, 'monthly_increase_minor' => 20000, 'effective_adjustment' => '6,66 %', 'retroactive_minor' => 60000, 'annual_impact_minor' => 266667], $reference, '1.1.0', 'Money::percentage e divisão inteira do Core.'),
            new GoldenCase('boundary-no-adjustment', 'Percentual e aumento fixo iguais a zero', GoldenCaseKind::Boundary,
                ['current_salary_minor' => 300000, 'adjustment_rate' => '0', 'fixed_addition_minor' => 0, 'retroactive_months' => 0], ['new_salary_minor' => 300000, 'monthly_increase_minor' => 0, 'effective_adjustment' => '0,00 %'], $reference, '1.1.0'),
            new GoldenCase('invalid-zero-salary', 'Salário zero é rejeitado', GoldenCaseKind::InvalidInput,
                ['current_salary' => '0,00'], ['outcome' => 'validation-error'], 'ExecuteToolRequest exige salário maior que zero.', '1.1.0'),
            new GoldenCase('rounding-third', 'Impacto anual absorve o terço de férias em centavos', GoldenCaseKind::Rounding,
                ['monthly_increase_minor' => 100], ['annual_impact_minor' => 1333], $reference, '1.1.0', 'diferença mensal × 40 ÷ 3 usando Money.'),
            new GoldenCase('non-applicable-collective-rule', 'Piso ou cláusula coletiva não informados não são inferidos', GoldenCaseKind::NonApplicable,
                ['scenario' => 'convencao-coletiva-com-regra-especifica'], ['outcome' => 'professional-review-required'], 'README exclui pisos, tetos, compensações e cláusulas específicas.', '1.1.0'),
            new GoldenCase('normative-transition', 'Mudança na composição anual exige revisão da regra', GoldenCaseKind::NormativeTransition,
                ['scenario' => 'alteracao-normativa-de-decimo-terceiro-ou-terco-de-ferias'], ['outcome' => 'rule-review-required'], 'Constituição Federal art. 7º, XVII e Lei 4.090/1962 fundamentam as parcelas hoje consideradas.', '1.1.0'),
            new GoldenCase('regression-effective-rate', 'Aumento fixo participa do reajuste efetivo', GoldenCaseKind::Regression,
                ['current_salary_minor' => 300000, 'adjustment_rate' => '5', 'fixed_addition_minor' => 5000], ['monthly_increase_minor' => 20000, 'effective_adjustment' => '6,66 %'], $reference, '1.1.0'),
        ]);
    }
}
