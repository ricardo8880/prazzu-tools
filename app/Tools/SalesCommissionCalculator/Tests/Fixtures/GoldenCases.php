<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: replace with an official source, reviewed calculation, or approved case.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'Parametric README rules and CalculatorTest regressions, reviewed for schema 1.2.0.';

        return new GoldenCaseSuite('sales-commission-calculator', [
            new GoldenCase('typical-with-goal', 'Commission after refunds/chargebacks with target reached', GoldenCaseKind::Typical,
                ['sales_minor' => 1000000, 'reversals_minor' => 50000, 'rate' => '5', 'goal_minor' => 800000, 'goal_bonus_rate' => '2'],
                ['commissionable_sales_minor' => 950000, 'base_commission_minor' => 47500, 'goal_bonus_minor' => 19000, 'total_commission_minor' => 66500, 'goal_achievement' => '118.75 %'], $reference, '1.2.0', 'Money::percentage; values in cents.'),
            new GoldenCase('boundary-all-reversed', 'Refunds equal to gross sales reduce the commissionable base to zero', GoldenCaseKind::Boundary,
                ['sales_minor' => 100000, 'reversals_minor' => 100000, 'rate' => '5', 'goal_minor' => 0, 'goal_bonus_rate' => '0'], ['commissionable_sales_minor' => 0, 'total_commission_minor' => 0], $reference, '1.2.0'),
            new GoldenCase('invalid-reversal-over-sales', 'Refunds above gross sales are rejected', GoldenCaseKind::InvalidInput,
                ['sales_minor' => 100000, 'reversals_minor' => 100001], ['outcome' => 'domain-error'], 'Calculator limits refunds/chargebacks to gross sales.', '1.2.0'),
            new GoldenCase('rounding-percentage', 'Fractional percentage uses the Core monetary rounding policy', GoldenCaseKind::Rounding,
                ['sales_minor' => 9999, 'reversals_minor' => 0, 'rate' => '1.5', 'goal_minor' => 0, 'goal_bonus_rate' => '0'], ['base_commission_minor' => 150], $reference, '1.2.0', 'Money::percentage rounds to cents.'),
            new GoldenCase('non-applicable-contract-rule', 'An unspecified contractual rule is not inferred', GoldenCaseKind::NonApplicable,
                ['scenario' => 'teto-ou-competencia-contratual-nao-informados'], ['outcome' => 'review-commercial-policy'], 'README treats caps, earning periods, and future returns as external rules.', '1.2.0'),
            new GoldenCase('normative-transition-parametric', 'A legal or contractual change requires parameter updates', GoldenCaseKind::NormativeTransition,
                ['scenario' => 'politica-de-comissao-alterada'], ['outcome' => 'user-supplied-rule'], 'The tool does not embed a statutory commission rate; parameters are supplied by the user.', '1.2.0'),
        ]);
    }
}
