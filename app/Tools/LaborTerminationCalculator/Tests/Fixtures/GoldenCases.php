<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $terminationReference = 'LaborTerminationCalculatorTest protege aviso proporcional, pedido de demissão, justa causa, acordo mútuo, contratos a termo, férias em dobro, artigos 479/480 e emprego doméstico.';
        $taxReference = 'PayrollTaxCalculatorTest protege faixas progressivas de INSS e redução do IRRF usadas pelo módulo para 2026; README referencia a Portaria Interministerial MPS/MF nº 13/2026.';

        return new GoldenCaseSuite(
            toolSlug: 'calculadora-de-rescisao',
            cases: [
                new GoldenCase(
                    identifier: 'typical',
                    title: 'Dispensa sem justa causa com aviso indenizado preserva projeção conhecida',
                    kind: GoldenCaseKind::Typical,
                    input: ['monthly_salary' => '3000.00', 'admission_date' => '2024-01-10', 'termination_date' => '2026-07-14', 'termination_type' => 'dismissal_without_cause', 'notice_type' => 'indemnified'],
                    expected: ['notice_days' => 36, 'notice_pay_minor' => 360000, 'projected_termination_date' => '2026-08-19', 'proportional_vacation_months' => 7, 'proportional_thirteenth_months' => 8],
                    reference: $terminationReference,
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Valores monetários são preservados em centavos pelo Money.',
                ),
                new GoldenCase(
                    identifier: 'boundary',
                    title: 'Contrato a termo rejeita aviso-prévio comum incompatível',
                    kind: GoldenCaseKind::Boundary,
                    input: ['contract_type' => 'fixed_term', 'termination_type' => 'contract_end', 'notice_type' => 'worked'],
                    expected: ['outcome' => 'domain-error'],
                    reference: $terminationReference,
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'invalid-input',
                    title: 'Combinação contratual inválida é rejeitada pelo domínio',
                    kind: GoldenCaseKind::InvalidInput,
                    input: ['contract_type' => 'fixed_term', 'notice_type' => 'worked', 'contract_end_date' => null],
                    expected: ['outcome' => 'domain-error'],
                    reference: $terminationReference,
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'rounding',
                    title: 'INSS de R$ 3.000 preserva centavos na progressividade de 2026',
                    kind: GoldenCaseKind::Rounding,
                    input: ['taxable_income' => '3000.00', 'competence' => '2026-01'],
                    expected: ['inss_minor' => 24858],
                    reference: $taxReference,
                    normativeRuleVersion: 'payroll-tax-2026',
                    roundingPolicy: 'Cada faixa previdenciária é tratada em centavos conforme política documentada no README.',
                ),
                new GoldenCase(
                    identifier: 'non-applicable',
                    title: 'Casos com estabilidade ou normas coletivas exigem cálculo especializado',
                    kind: GoldenCaseKind::NonApplicable,
                    input: ['scenario' => 'collective-bargaining-or-employment-stability'],
                    expected: ['outcome' => 'explicitly-out-of-scope'],
                    reference: 'README do módulo declara estabilidade, normas coletivas, afastamentos, médias complexas e decisões judiciais como cenários que podem exigir cálculo especializado.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'normative-transition',
                    title: 'Tabela previdenciária de 2026 possui regressão explícita',
                    kind: GoldenCaseKind::NormativeTransition,
                    input: ['competence' => '2026-01', 'taxable_income' => '3000.00'],
                    expected: ['inss_minor' => 24858],
                    reference: $taxReference,
                    normativeRuleVersion: 'payroll-tax-2026',
                ),
                new GoldenCase(
                    identifier: 'regression',
                    title: 'Emprego doméstico não aplica multa de quarenta por cento sobre o saldo informado',
                    kind: GoldenCaseKind::Regression,
                    input: ['contract_type' => 'domestic', 'termination_type' => 'dismissal_without_cause', 'fgts_balance' => '10000.00', 'domestic_indemnity_reserve_balance' => '4000.00'],
                    expected: ['domestic_compensatory_deposit' => 'positive', 'penalty_rule' => 'reserve-plus-termination-deposit'],
                    reference: $terminationReference,
                    normativeRuleVersion: '1.0.0',
                ),
            ],
        );
    }
}
