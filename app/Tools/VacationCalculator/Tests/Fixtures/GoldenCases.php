<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';
    public const CLT_REFERENCE = 'CLT, arts. 129, 130, 142, 143 e 145; Constituição Federal, art. 7º, XVII. Revisão interna aprovada.';

    public static function suite(): GoldenCaseSuite
    {
        return new GoldenCaseSuite(
            toolSlug: 'calculadora-ferias',
            cases: [
                new GoldenCase('full-vacation-30-days', 'Férias integrais de 30 dias com terço constitucional', GoldenCaseKind::Typical, ['monthly_salary_minor' => 300000, 'unjustified_absences' => 0, 'cash_allowance' => false], ['entitled_days' => 30, 'leave_days' => 30, 'gross_total_minor' => 400000], self::CLT_REFERENCE),
                new GoldenCase('absence-band-24-days', 'Seis faltas injustificadas reduzem o direito para 24 dias', GoldenCaseKind::Boundary, ['monthly_salary_minor' => 300000, 'unjustified_absences' => 6, 'cash_allowance' => false], ['entitled_days' => 24, 'gross_total_minor' => 320000], self::CLT_REFERENCE),
                new GoldenCase('zero-salary-rejected', 'Salário mensal zerado é inválido', GoldenCaseKind::InvalidInput, ['monthly_salary_minor' => 0, 'unjustified_absences' => 0, 'cash_allowance' => false], ['exception' => 'InvalidValue'], self::CLT_REFERENCE),
                new GoldenCase('minor-unit-rounding', 'Terço constitucional arredondado em menor unidade', GoldenCaseKind::Rounding, ['monthly_salary_minor' => 100, 'unjustified_absences' => 0, 'cash_allowance' => false], ['gross_total_minor' => 133], self::CLT_REFERENCE),
                new GoldenCase('absence-loss-of-entitlement', 'Mais de 32 faltas elimina o direito do período', GoldenCaseKind::NonApplicable, ['monthly_salary_minor' => 300000, 'unjustified_absences' => 33, 'cash_allowance' => false], ['entitled_days' => 0, 'gross_total_minor' => 0], self::CLT_REFERENCE),
                new GoldenCase('absence-transition-five-to-six', 'Transição normativa entre cinco e seis faltas', GoldenCaseKind::NormativeTransition, ['monthly_salary_minor' => 300000, 'unjustified_absences' => 5, 'cash_allowance' => false], ['entitled_days' => 30, 'gross_total_minor' => 400000], self::CLT_REFERENCE),
                new GoldenCase('cash-allowance-regression', 'Conversão de um terço do período em abono', GoldenCaseKind::Regression, ['monthly_salary_minor' => 300000, 'unjustified_absences' => 0, 'cash_allowance' => true], ['leave_days' => 20, 'cash_allowance_days' => 10, 'gross_total_minor' => 400000], self::CLT_REFERENCE),
            ],
        );
    }
}
