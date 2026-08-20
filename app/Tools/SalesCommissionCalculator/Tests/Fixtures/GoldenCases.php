<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'Regras paramétricas do README e regressões do CalculatorTest, revisadas para o schema 1.2.0.';

        return new GoldenCaseSuite('comissao-vendedores', [
            new GoldenCase('typical-with-goal', 'Comissão líquida de estornos com meta atingida', GoldenCaseKind::Typical,
                ['sales_minor' => 1000000, 'reversals_minor' => 50000, 'rate' => '5', 'goal_minor' => 800000, 'goal_bonus_rate' => '2'],
                ['commissionable_sales_minor' => 950000, 'base_commission_minor' => 47500, 'goal_bonus_minor' => 19000, 'total_commission_minor' => 66500, 'goal_achievement' => '118,75 %'], $reference, '1.2.0', 'Money::percentage; valores em centavos.'),
            new GoldenCase('boundary-all-reversed', 'Estorno igual ao faturamento zera a base', GoldenCaseKind::Boundary,
                ['sales_minor' => 100000, 'reversals_minor' => 100000, 'rate' => '5', 'goal_minor' => 0, 'goal_bonus_rate' => '0'], ['commissionable_sales_minor' => 0, 'total_commission_minor' => 0], $reference, '1.2.0'),
            new GoldenCase('invalid-reversal-over-sales', 'Estorno superior ao faturamento é rejeitado', GoldenCaseKind::InvalidInput,
                ['sales_minor' => 100000, 'reversals_minor' => 100001], ['outcome' => 'domain-error'], 'Calculator limita estornos ao faturamento bruto.', '1.2.0'),
            new GoldenCase('rounding-percentage', 'Percentual fracionário usa arredondamento monetário do Core', GoldenCaseKind::Rounding,
                ['sales_minor' => 9999, 'reversals_minor' => 0, 'rate' => '1.5', 'goal_minor' => 0, 'goal_bonus_rate' => '0'], ['base_commission_minor' => 150], $reference, '1.2.0', 'Money::percentage arredonda para centavos.'),
            new GoldenCase('non-applicable-contract-rule', 'Regra contratual não informada não é inferida', GoldenCaseKind::NonApplicable,
                ['scenario' => 'teto-ou-competencia-contratual-nao-informados'], ['outcome' => 'review-commercial-policy'], 'README delimita teto, competência e devoluções futuras como regras externas.', '1.2.0'),
            new GoldenCase('normative-transition-parametric', 'Mudança legal ou contratual exige atualização dos parâmetros', GoldenCaseKind::NormativeTransition,
                ['scenario' => 'politica-de-comissao-alterada'], ['outcome' => 'user-supplied-rule'], 'A ferramenta não embute percentual legal de comissão; os parâmetros são informados pelo usuário.', '1.2.0'),
        ]);
    }
}
