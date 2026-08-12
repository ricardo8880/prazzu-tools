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
        $reference = 'README e CalculatorTest do módulo AssetDepreciationCalculator 1.0.0.';

        return new GoldenCaseSuite(toolSlug: 'calculadora-depreciacao-ativos', cases: [
            new GoldenCase('typical', 'Ativo linear em cinco anos', GoldenCaseKind::Typical, ['value' => '12000', 'years' => 5], ['monthly' => 'R$ 200,00', 'annual' => 'R$ 2.400,00'], $reference, '1.0.0', 'Money/IntegerRounding sem float.'),
            new GoldenCase('boundary', 'Vida útil mínima de um ano', GoldenCaseKind::Boundary, ['value' => '1000', 'years' => 1], ['book_value_end' => 'R$ 0,00'], $reference, '1.0.0'),
            new GoldenCase('invalid-input', 'Valor zero é rejeitado', GoldenCaseKind::InvalidInput, ['value' => '0'], ['outcome' => 'validation-error'], 'ExecuteToolRequest exige valor maior que zero.', '1.0.0'),
            new GoldenCase('rounding', 'Centavos são absorvidos sem saldo negativo', GoldenCaseKind::Rounding, ['value' => '1000.01', 'years' => 3], ['book_value_end' => 'R$ 0,00'], $reference, '1.0.0', 'A projeção absorve diferenças de centavos no último período.'),
            new GoldenCase('non-applicable', 'Vida útil deve ser definida externamente', GoldenCaseKind::NonApplicable, ['scenario' => 'vida-util-nao-definida'], ['outcome' => 'parameter-required'], 'README não infere vida útil ou enquadramento.', '1.0.0'),
            new GoldenCase('normative-transition', 'Política contábil não é inferida', GoldenCaseKind::NormativeTransition, ['scenario' => 'policy-change'], ['outcome' => 'user-supplied-parameters'], 'A ferramenta é paramétrica e não embute taxa normativa.', '1.0.0'),
            new GoldenCase('regression', 'Soma dos dígitos fecha o saldo em zero', GoldenCaseKind::Regression, ['value' => '15000', 'years' => 5, 'method' => 'sum_of_years_digits'], ['first_year' => 'R$ 5.000,00', 'book_value_end' => 'R$ 0,00'], $reference, '1.0.0'),
        ]);
    }
}
