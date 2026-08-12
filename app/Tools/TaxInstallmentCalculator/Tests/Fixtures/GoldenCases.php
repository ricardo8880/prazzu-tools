<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'README e CalculatorTest do módulo TaxInstallmentCalculator 1.0.0.';

        return new GoldenCaseSuite(toolSlug: 'calculadora-parcelamento-tributario', cases: [
            new GoldenCase('typical', 'Doze mil em doze parcelas a 1% a.m.', GoldenCaseKind::Typical, ['debt' => '12000', 'installments' => 12, 'rate' => '1'], ['average' => 'R$ 1.065,00', 'final_cost' => 'R$ 12.780,00'], $reference, '1.0.0', 'SAC paramétrico em centavos.'),
            new GoldenCase('boundary', 'Uma parcela sem encargos', GoldenCaseKind::Boundary, ['debt' => '1000', 'installments' => 1, 'rate' => '0'], ['payment' => 'R$ 1.000,00'], $reference, '1.0.0'),
            new GoldenCase('invalid-input', 'Entrada igual à dívida é rejeitada', GoldenCaseKind::InvalidInput, ['debt' => '1000', 'entry' => '1000'], ['outcome' => 'validation-error'], 'ExecuteToolRequest exige entrada menor que a dívida.', '1.0.0'),
            new GoldenCase('rounding', 'Principal com centavos fecha saldo', GoldenCaseKind::Rounding, ['debt' => '1000.01', 'installments' => 3, 'rate' => '0'], ['closing_balance' => 'R$ 0,00'], $reference, '1.0.0'),
            new GoldenCase('non-applicable', 'Regra oficial não é inferida', GoldenCaseKind::NonApplicable, ['scenario' => 'programa-nao-informado'], ['outcome' => 'user-supplied-rate'], 'README define cálculo paramétrico.', '1.0.0'),
            new GoldenCase('normative-transition', 'Mudança de programa tributário não altera a fórmula interna', GoldenCaseKind::NormativeTransition, ['scenario' => 'rule-change'], ['outcome' => 'confirm-official-conditions'], 'A ferramenta não embute regras de programas oficiais.', '1.0.0'),
            new GoldenCase('regression', 'SAC reduz parcela conforme saldo', GoldenCaseKind::Regression, ['debt' => '12000', 'installments' => 12, 'rate' => '1'], ['first' => 'R$ 1.120,00', 'last' => 'R$ 1.010,00'], $reference, '1.0.0'),
        ]);
    }
}
