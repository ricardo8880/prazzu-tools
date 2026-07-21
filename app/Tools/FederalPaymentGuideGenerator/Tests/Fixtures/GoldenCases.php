<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const NORMATIVE_VERSION = '2026.1.0';

    private const REFERENCE = 'Receita Federal: multa de mora de 0,33% ao dia, limitada a 20%; juros Selic acumulada informada pelo usuário. Revisado em 21/07/2026.';

    public static function suite(): GoldenCaseSuite
    {
        return new GoldenCaseSuite('gerador-darf-gps', [
            new GoldenCase('on-time-payment', 'Pagamento no vencimento', GoldenCaseKind::Typical, ['principal_minor' => 100000, 'due_date' => '2026-01-10', 'payment_date' => '2026-01-10', 'selic' => '0'], ['days_late' => 0, 'penalty_percent' => '0.00', 'penalty_minor' => 0, 'interest_minor' => 0, 'total_minor' => 100000], self::REFERENCE, self::NORMATIVE_VERSION),
            new GoldenCase('penalty-cap', 'Multa limitada a vinte por cento', GoldenCaseKind::Boundary, ['principal_minor' => 100000, 'due_date' => '2026-01-01', 'payment_date' => '2026-04-11', 'selic' => '5'], ['days_late' => 100, 'penalty_percent' => '20.00', 'penalty_minor' => 20000, 'interest_minor' => 5000, 'total_minor' => 125000], self::REFERENCE, self::NORMATIVE_VERSION),
            new GoldenCase('payment-before-due-date', 'Pagamento anterior ao vencimento', GoldenCaseKind::InvalidInput, ['principal_minor' => 100000, 'due_date' => '2026-01-10', 'payment_date' => '2026-01-09', 'selic' => '0'], ['exception' => 'InvalidArgumentException'], self::REFERENCE, self::NORMATIVE_VERSION),
            new GoldenCase('zero-principal', 'Principal zerado', GoldenCaseKind::NonApplicable, ['principal_minor' => 0, 'due_date' => '2026-01-01', 'payment_date' => '2026-01-11', 'selic' => '1'], ['penalty_minor' => 0, 'interest_minor' => 0, 'total_minor' => 0], self::REFERENCE, self::NORMATIVE_VERSION),
            new GoldenCase('minor-unit-rounding', 'Arredondamento em menor unidade monetária', GoldenCaseKind::Rounding, ['principal_minor' => 1, 'due_date' => '2026-01-01', 'payment_date' => '2026-01-02', 'selic' => '0'], ['penalty_percent' => '0.33', 'penalty_minor' => 0, 'total_minor' => 1], self::REFERENCE, self::NORMATIVE_VERSION, 'Money::percentage arredonda para a menor unidade monetária.'),
            new GoldenCase('gps-company-weekend', 'GPS empresa com vencimento em fim de semana', GoldenCaseKind::NormativeTransition, ['operation' => 'gps_company_monthly', 'competence' => '2026-05-01'], ['due_date' => '2026-06-19'], self::REFERENCE, self::NORMATIVE_VERSION),
            new GoldenCase('ten-days-late-regression', 'Regressão de dez dias de atraso', GoldenCaseKind::Regression, ['principal_minor' => 100000, 'due_date' => '2026-01-01', 'payment_date' => '2026-01-11', 'selic' => '1'], ['days_late' => 10, 'penalty_percent' => '3.30', 'penalty_minor' => 3300, 'interest_minor' => 1000, 'total_minor' => 104300], self::REFERENCE, self::NORMATIVE_VERSION),
        ]);
    }
}
