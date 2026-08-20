<?php

declare(strict_types=1);

namespace App\Tools\BreakEvenCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'Fórmula de margem de contribuição e regressões do CalculatorTest, revisadas para o schema 1.2.0.';

        return new GoldenCaseSuite('ponto-de-equilibrio', [
            new GoldenCase('typical', 'Custos fixos de 10 mil com contribuição de 40 reais', GoldenCaseKind::Typical,
                ['fixed_costs_minor' => 1000000, 'sale_price_minor' => 10000, 'variable_cost_minor' => 6000],
                ['break_even_units' => 250, 'break_even_revenue_minor' => 2500000, 'contribution_margin' => '40,00 %', 'coverage_surplus_minor' => 0], $reference, '1.2.0', 'Quantidade arredondada para cima; valores em centavos.'),
            new GoldenCase('boundary-zero-fixed', 'Sem custos fixos o ponto de equilíbrio é zero', GoldenCaseKind::Boundary,
                ['fixed_costs_minor' => 0, 'sale_price_minor' => 10000, 'variable_cost_minor' => 6000], ['break_even_units' => 0, 'break_even_revenue_minor' => 0], $reference, '1.2.0'),
            new GoldenCase('invalid-zero-contribution', 'Preço igual ao custo variável é rejeitado', GoldenCaseKind::InvalidInput,
                ['fixed_costs_minor' => 100000, 'sale_price_minor' => 5000, 'variable_cost_minor' => 5000], ['outcome' => 'domain-error'], 'Calculator rejeita margem de contribuição não positiva.', '1.2.0'),
            new GoldenCase('rounding-whole-unit', 'Quantidade fracionária é elevada à próxima unidade', GoldenCaseKind::Rounding,
                ['fixed_costs_minor' => 10000, 'sale_price_minor' => 1000, 'variable_cost_minor' => 700], ['break_even_units' => 34, 'coverage_surplus_minor' => 200], $reference, '1.2.0', 'teto(10000 / 300) = 34 unidades.'),
            new GoldenCase('non-applicable-variable-cost-omitted', 'Tributo variável omitido invalida a interpretação gerencial', GoldenCaseKind::NonApplicable,
                ['scenario' => 'custo-variavel-incompleto'], ['outcome' => 'user-must-complete-variable-cost'], 'README exige incluir tributos, comissões e perdas variáveis quando aplicáveis.', '1.2.0'),
        ]);
    }
}
