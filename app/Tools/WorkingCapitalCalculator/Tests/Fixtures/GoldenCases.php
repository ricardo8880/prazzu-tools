<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'Fórmulas descritas no README e regressões do CalculatorTest, revisadas para o schema 1.2.0.';

        return new GoldenCaseSuite('capital-de-giro', [
            new GoldenCase('typical-deficit', 'NCG parcialmente descoberta pelo CCL', GoldenCaseKind::Typical,
                ['cash_minor' => 1000000, 'receivables_minor' => 5000000, 'inventory_minor' => 3000000, 'other_current_assets_minor' => 500000, 'suppliers_minor' => 2500000, 'other_operating_liabilities_minor' => 500000, 'loans_minor' => 1000000, 'other_current_liabilities_minor' => 500000],
                ['required_capital_minor' => 5500000, 'net_working_capital_minor' => 5000000, 'funding_gap_minor' => 500000, 'funding_surplus_minor' => 0], $reference, '1.2.0', 'Centavos inteiros; sem float.'),
            new GoldenCase('boundary-zero-need', 'Passivos operacionais cobrem os ativos operacionais', GoldenCaseKind::Boundary,
                ['cash_minor' => 1000000, 'operating_assets_minor' => 2000000, 'operating_liabilities_minor' => 2500000, 'financial_liabilities_minor' => 0],
                ['required_capital_minor' => 0, 'operating_need_minor' => -500000], $reference, '1.2.0'),
            new GoldenCase('invalid-negative-balance', 'Saldo negativo é rejeitado na entrada', GoldenCaseKind::InvalidInput,
                ['cash' => '-1,00'], ['outcome' => 'validation-error'], 'ExecuteToolRequest exige money_min:0 em todos os saldos.', '1.2.0'),
            new GoldenCase('rounding-cents', 'Centavos são preservados no cálculo da NCG', GoldenCaseKind::Rounding,
                ['receivables_minor' => 100001, 'inventory_minor' => 200002, 'other_current_assets_minor' => 3, 'suppliers_minor' => 100000, 'other_operating_liabilities_minor' => 1],
                ['operating_need_minor' => 200005], $reference, '1.2.0', 'Operações exclusivamente em centavos inteiros.'),
            new GoldenCase('non-applicable-mixed-dates', 'Saldos de datas-base diferentes não formam diagnóstico comparável', GoldenCaseKind::NonApplicable,
                ['scenario' => 'saldos-de-periodos-diferentes'], ['outcome' => 'user-must-normalize-period'], 'README exige a mesma data-base e o mesmo perímetro contabilístico.', '1.2.0'),
        ]);
    }
}
