<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'Fórmulas do README e regressões do CalculatorTest, revisadas para o schema 1.2.0.';

        return new GoldenCaseSuite('fluxo-de-caixa', [
            new GoldenCase('typical-positive', 'Período com geração e saldo final positivos', GoldenCaseKind::Typical,
                ['opening_balance_minor' => 500000, 'sales_receipts_minor' => 2000000, 'other_inflows_minor' => 100000, 'operating_payments_minor' => 1200000, 'tax_payments_minor' => 200000, 'investments_minor' => 100000, 'financing_payments_minor' => 50000, 'other_outflows_minor' => 50000],
                ['closing_balance_minor' => 1000000, 'net_movement_minor' => 500000, 'operating_generation_minor' => 600000], $reference, '1.2.0', 'Centavos inteiros; sem float.'),
            new GoldenCase('boundary-zero-closing', 'Movimento líquido consome exatamente o saldo inicial', GoldenCaseKind::Boundary,
                ['opening_balance_minor' => 100000, 'total_inflows_minor' => 200000, 'total_outflows_minor' => 300000],
                ['closing_balance_minor' => 0], $reference, '1.2.0'),
            new GoldenCase('invalid-negative-outflow', 'Saída negativa é rejeitada', GoldenCaseKind::InvalidInput,
                ['operating_payments' => '-0,01'], ['outcome' => 'validation-error'], 'ExecuteToolRequest exige money_min:0 nas movimentações.', '1.2.0'),
            new GoldenCase('rounding-cents', 'Entradas e saídas preservam centavos', GoldenCaseKind::Rounding,
                ['opening_balance_minor' => 1, 'total_inflows_minor' => 102, 'total_outflows_minor' => 100], ['closing_balance_minor' => 3], $reference, '1.2.0', 'Somas e subtrações em centavos inteiros.'),
            new GoldenCase('non-applicable-accrual', 'Valores por competência não devem ser tratados como fluxo realizado', GoldenCaseKind::NonApplicable,
                ['scenario' => 'receitas-e-despesas-sem-data-de-caixa'], ['outcome' => 'use-cash-basis-values'], 'README limita o cálculo ao regime de caixa de um único período.', '1.2.0'),
        ]);
    }
}
