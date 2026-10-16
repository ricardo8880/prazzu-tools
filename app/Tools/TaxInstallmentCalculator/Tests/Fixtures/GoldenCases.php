<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: replace with an official source, reviewed calculation, or approved case.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'TaxInstallmentCalculator 1.0.0 README and CalculatorTest.';

        return new GoldenCaseSuite(toolSlug: 'irs-payment-plan-calculator', cases: [
            new GoldenCase('typical', 'Twelve thousand over twelve monthly payments at 1% per month', GoldenCaseKind::Typical, ['debt' => '12000', 'installments' => 12, 'rate' => '1'], ['average' => '$1,065.00', 'final_cost' => '$12,780.00'], $reference, '1.0.0', 'Parametric equal-principal model in cents.'),
            new GoldenCase('boundary', 'One payment with no charges', GoldenCaseKind::Boundary, ['debt' => '1000', 'installments' => 1, 'rate' => '0'], ['payment' => '$1,000.00'], $reference, '1.0.0'),
            new GoldenCase('invalid-input', 'An upfront payment equal to the tax balance is rejected', GoldenCaseKind::InvalidInput, ['debt' => '1000', 'entry' => '1000'], ['outcome' => 'validation-error'], 'ExecuteToolRequest requires the upfront payment to be below the tax balance.', '1.0.0'),
            new GoldenCase('rounding', 'Principal with cents closes the balance', GoldenCaseKind::Rounding, ['debt' => '1000.01', 'installments' => 3, 'rate' => '0'], ['closing_balance' => '$0.00'], $reference, '1.0.0'),
            new GoldenCase('non-applicable', 'An official program rule is not inferred', GoldenCaseKind::NonApplicable, ['scenario' => 'program-not-specified'], ['outcome' => 'user-supplied-rate'], 'README defines a parametric calculation.', '1.0.0'),
            new GoldenCase('normative-transition', 'A tax-program change does not alter the internal formula', GoldenCaseKind::NormativeTransition, ['scenario' => 'rule-change'], ['outcome' => 'confirm-official-conditions'], 'The tool does not embed rules from official programs.', '1.0.0'),
            new GoldenCase('regression', 'Equal-principal payments decline as the balance falls', GoldenCaseKind::Regression, ['debt' => '12000', 'installments' => 12, 'rate' => '1'], ['first' => '$1,120.00', 'last' => '$1,010.00'], $reference, '1.0.0'),
        ]);
    }
}
