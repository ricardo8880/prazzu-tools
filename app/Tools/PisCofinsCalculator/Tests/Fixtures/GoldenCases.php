<?php

declare(strict_types=1);

namespace App\Tools\PisCofinsCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'Lei 9.718/1998; Leis 10.637/2002 e 10.833/2003; LC 214/2025; orientações RFB para 2026; README e CalculatorTest do módulo.';

        return new GoldenCaseSuite(toolSlug: 'calculadora-pis-cofins', cases: [
            new GoldenCase('typical', 'Cumulativo sobre R$ 10.000', GoldenCaseKind::Typical, ['period' => '2026-08', 'regime' => 'cumulative', 'taxable_revenue' => '10000'], ['pis' => 'R$ 65,00', 'cofins' => 'R$ 300,00', 'total' => 'R$ 365,00'], $reference, 'pis_cofins.general_2026:2026.1.0', 'Money::percentage com HalfUp.'),
            new GoldenCase('boundary', 'Crédito igual ao débito zera o recolhimento não cumulativo', GoldenCaseKind::Boundary, ['revenue' => '10000', 'credit_base' => '10000'], ['total' => 'R$ 0,00', 'credit_balance' => 'R$ 0,00'], $reference, 'pis_cofins.general_2026:2026.1.0'),
            new GoldenCase('invalid-input', 'Base tributável total zero é rejeitada', GoldenCaseKind::InvalidInput, ['taxable_revenue' => '0'], ['outcome' => 'validation-error'], 'ExecuteToolRequest e Calculator exigem base tributável positiva.', '1.0.0'),
            new GoldenCase('rounding', 'Centavos preservam arredondamento sem float', GoldenCaseKind::Rounding, ['revenue' => '1234.56', 'regime' => 'cumulative'], ['policy' => 'HalfUp-centavos'], $reference, 'pis_cofins.general_2026:2026.1.0', 'Money em centavos e Percentage escalada; HalfUp em cada contribuição.'),
            new GoldenCase('non-applicable', 'Monofasia e regimes especiais não são inferidos', GoldenCaseKind::NonApplicable, ['scenario' => 'monophase-or-special-rate'], ['outcome' => 'explicitly-not-covered'], 'README e interface exigem base previamente classificada; a ferramenta aplica apenas alíquotas gerais.', '1.0.0'),
            new GoldenCase('normative-transition', 'Competência 2026 registra transição CBS/IBS', GoldenCaseKind::NormativeTransition, ['period' => '2026-08'], ['rules' => ['pis_cofins.general_2026:2026.1.0'], 'warning' => 'CBS/IBS transition'], $reference, '2026.1.0'),
            new GoldenCase('regression', 'Não cumulativo desconta créditos na mesma alíquota', GoldenCaseKind::Regression, ['revenue' => '10000', 'credit_base' => '4000'], ['pis' => 'R$ 99,00', 'cofins' => 'R$ 456,00', 'total' => 'R$ 555,00'], $reference, 'pis_cofins.general_2026:2026.1.0', 'Débito e crédito calculados separadamente com Money::percentage.'),
        ]);
    }
}
