<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: replace with an official source, reviewed calculation, or approved case.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'Formulas described in the README and CalculatorTest regressions, reviewed for schema 1.2.0.';

        return new GoldenCaseSuite('working-capital-calculator', [
            new GoldenCase('typical-deficit', 'Operating need partially uncovered by net working capital', GoldenCaseKind::Typical,
                ['cash_minor' => 1000000, 'receivables_minor' => 5000000, 'inventory_minor' => 3000000, 'other_current_assets_minor' => 500000, 'suppliers_minor' => 2500000, 'other_operating_liabilities_minor' => 500000, 'loans_minor' => 1000000, 'other_current_liabilities_minor' => 500000],
                ['required_capital_minor' => 5500000, 'net_working_capital_minor' => 5000000, 'funding_gap_minor' => 500000, 'funding_surplus_minor' => 0], $reference, '1.2.0', 'Integer cents; no floats.'),
            new GoldenCase('boundary-zero-need', 'Operating liabilities cover operating assets', GoldenCaseKind::Boundary,
                ['cash_minor' => 1000000, 'operating_assets_minor' => 2000000, 'operating_liabilities_minor' => 2500000, 'financial_liabilities_minor' => 0],
                ['required_capital_minor' => 0, 'operating_need_minor' => -500000], $reference, '1.2.0'),
            new GoldenCase('invalid-negative-balance', 'A negative balance is rejected on input', GoldenCaseKind::InvalidInput,
                ['cash' => '-1.00'], ['outcome' => 'validation-error'], 'ExecuteToolRequest requires money_min:0 for all balances.', '1.2.0'),
            new GoldenCase('rounding-cents', 'Cents are preserved in the operating-need calculation', GoldenCaseKind::Rounding,
                ['receivables_minor' => 100001, 'inventory_minor' => 200002, 'other_current_assets_minor' => 3, 'suppliers_minor' => 100000, 'other_operating_liabilities_minor' => 1],
                ['operating_need_minor' => 200005], $reference, '1.2.0', 'Operations use integer cents exclusively.'),
            new GoldenCase('non-applicable-mixed-dates', 'Balances from different reporting dates do not produce a comparable analysis', GoldenCaseKind::NonApplicable,
                ['scenario' => 'balances-from-different-periods'], ['outcome' => 'user-must-normalize-period'], 'README requires the same reporting date and accounting scope.', '1.2.0'),
        ]);
    }
}
