<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'Lei 9.249/1995, Lei 9.430/1996, LC 224/2025 e RFB Perguntas e Respostas — Redução dos Incentivos e Benefícios Tributários V5 (30/07/2026); documentação normativa do módulo e CalculatorTest.';

        return new GoldenCaseSuite(
            toolSlug: 'calculadora-irpj-csll-lucro-presumido',
            cases: [
                new GoldenCase('typical', 'Comércio no 1º trimestre sem exceder a faixa normal', GoldenCaseKind::Typical,
                    ['quarter' => 1, 'commerce_revenue' => '1000000.00'],
                    ['irpj_base' => 'R$ 80.000,00', 'irpj_due' => 'R$ 14.000,00', 'csll_base' => 'R$ 120.000,00', 'csll_due' => 'R$ 10.800,00', 'total_due' => 'R$ 24.800,00'],
                    $reference, 'lucro_presumido.irpj_csll:2026.1.0', 'Money em centavos e Percentage escalada; HalfUp.'),
                new GoldenCase('boundary', 'Receita exatamente no limite trimestral de R$ 1,25 milhão', GoldenCaseKind::Boundary,
                    ['quarter' => 1, 'commerce_revenue' => '1250000.00'],
                    ['irpj_normal_allowance' => 'R$ 1.250.000,00', 'irpj_base' => 'R$ 100.000,00'],
                    $reference, 'lucro_presumido.irpj_csll:2026.1.0'),
                new GoldenCase('invalid-input', 'Sem receita bruta o cálculo é rejeitado', GoldenCaseKind::InvalidInput,
                    ['quarter' => 1, 'all_activity_revenue' => '0'], ['outcome' => 'validation-error'],
                    'ExecuteToolRequest e Calculator exigem receita positiva em ao menos uma atividade.', '1.0.0'),
                new GoldenCase('rounding', 'Rateio entre atividades preserva centavos sem float', GoldenCaseKind::Rounding,
                    ['quarter' => 1, 'commerce_revenue' => '1000000.01', 'services_revenue' => '500000.02'],
                    ['policy' => 'proportional-HalfUp-centavos'], $reference, 'lucro_presumido.irpj_csll:2026.1.0',
                    'Rateio proporcional reduz a fração por MDC e usa IntegerRounding HalfUp; percentuais são aplicados por Money::percentage.'),
                new GoldenCase('non-applicable', 'Instituições financeiras e regimes especiais ficam fora do escopo', GoldenCaseKind::NonApplicable,
                    ['scenario' => 'financial-institution-or-special-sector'], ['outcome' => 'explicitly-not-covered'],
                    'README, interface e NORMATIVE_RULES limitam o escopo a pessoas jurídicas em geral no lucro presumido.', '1.0.0'),
                new GoldenCase('normative-transition', 'CSLL aplica aumento somente a partir do 2º trimestre de 2026', GoldenCaseKind::NormativeTransition,
                    ['q1_revenue' => '2000000.00', 'q2_revenue' => '2000000.00'],
                    ['q1_csll_normal_revenue' => 'R$ 2.000.000,00', 'q2_csll_normal_allowance' => 'R$ 1.250.000,00'],
                    $reference, 'lucro_presumido.irpj_csll:2026.1.0'),
                new GoldenCase('regression', 'Adicional de IRPJ incide sobre base trimestral acima de R$ 60 mil', GoldenCaseKind::Regression,
                    ['quarter' => 1, 'commerce_revenue' => '1000000.00'],
                    ['irpj_main' => 'R$ 12.000,00', 'additional_base' => 'R$ 20.000,00', 'additional_irpj' => 'R$ 2.000,00', 'irpj_due' => 'R$ 14.000,00'],
                    $reference, 'lucro_presumido.irpj_csll:2026.1.0', 'Money em centavos; HalfUp.'),
            ],
        );
    }
}
