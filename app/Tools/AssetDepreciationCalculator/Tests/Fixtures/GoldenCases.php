<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'README e CalculatorTest do módulo AssetDepreciationCalculator 1.1.0.';

        return new GoldenCaseSuite('calculadora-depreciacao-ativos', [
            new GoldenCase('typical-residual', 'Ativo linear com valor residual', GoldenCaseKind::Typical,
                ['value_minor' => 1200000, 'residual_value_minor' => 200000, 'years' => 5, 'method' => 'linear'],
                ['depreciable_base_minor' => 1000000, 'monthly_minor' => 16667, 'annual_minor' => 200000, 'book_value_year_one_minor' => 1000000, 'book_value_end_minor' => 200000], $reference, '1.1.0', 'Money/IntegerRounding sem float.'),
            new GoldenCase('boundary-one-year', 'Vida útil mínima preserva o residual', GoldenCaseKind::Boundary,
                ['value_minor' => 100000, 'residual_value_minor' => 10000, 'years' => 1], ['depreciation_minor' => 90000, 'book_value_end_minor' => 10000], $reference, '1.1.0'),
            new GoldenCase('invalid-residual-equals-cost', 'Residual igual ao valor do bem é rejeitado', GoldenCaseKind::InvalidInput,
                ['value_minor' => 100000, 'residual_value_minor' => 100000], ['outcome' => 'validation-or-domain-error'], 'ExecuteToolRequest e Calculator exigem residual menor que o valor do bem.', '1.1.0'),
            new GoldenCase('rounding-linear', 'Centavos fecham exatamente no valor residual', GoldenCaseKind::Rounding,
                ['value_minor' => 100001, 'residual_value_minor' => 10000, 'years' => 3, 'method' => 'linear'], ['book_value_end_minor' => 10000], $reference, '1.1.0', 'Diferença de centavos é absorvida no último ano.'),
            new GoldenCase('non-applicable-missing-policy', 'Vida útil e residual precisam vir da política aplicável', GoldenCaseKind::NonApplicable,
                ['scenario' => 'vida-util-ou-residual-nao-definidos'], ['outcome' => 'parameter-required'], 'README não infere vida útil, residual, taxa fiscal ou elegibilidade do ativo.', '1.1.0'),
            new GoldenCase('normative-transition-parametric', 'Mudança de política não altera parâmetros silenciosamente', GoldenCaseKind::NormativeTransition,
                ['scenario' => 'policy-change'], ['outcome' => 'user-supplied-parameters'], 'O módulo é paramétrico; mudanças contábeis/fiscais exigem revisão dos dados informados.', '1.1.0'),
        ]);
    }
}
