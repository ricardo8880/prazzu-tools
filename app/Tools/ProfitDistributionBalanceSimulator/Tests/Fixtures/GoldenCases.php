<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: replace with an official source, reviewed calculation, or approved case.';

    public static function suite(): GoldenCaseSuite
    {
        return new GoldenCaseSuite(toolSlug: 'owner-distribution-planner', cases: [new GoldenCase('typical', 'Revenue 240k and net income 72k', GoldenCaseKind::Typical, ['case' => 'typical'], ['outcome' => 'calculated'], 'User-entered accounting parameters; tax treatment must be confirmed for the specific case.', '1.0.0'), new GoldenCase('boundary', 'Minimum valid value', GoldenCaseKind::Boundary, ['value' => '0.01'], ['outcome' => 'calculated'], 'Module validation and CalculatorTest.', '1.0.0'), new GoldenCase('invalid-input', 'Required field missing', GoldenCaseKind::InvalidInput, ['required' => 'missing'], ['outcome' => 'validation-error'], 'Module ExecuteToolRequest.', '1.0.0'), new GoldenCase('rounding', 'Rounding in cents', GoldenCaseKind::Rounding, ['case' => 'rounding'], ['outcome' => 'integer-cents'], 'Value object Money do Core.', '1.0.0'), new GoldenCase('non-applicable', 'Rule not inferred automatically', GoldenCaseKind::NonApplicable, ['rule' => 'unknown'], ['outcome' => 'user-parameter'], 'Module README.', '1.0.0'), new GoldenCase('normative-transition', 'Rule change requires confirmation', GoldenCaseKind::NormativeTransition, ['year' => 2027], ['outcome' => 'verify-rule'], 'Module README and referenced rule source.', '1.0.0'), new GoldenCase('regression','Primary scenario remains stable',GoldenCaseKind::Regression,['case' => 'regression'],['outcome' => 'calculated'],'Module CalculatorTest.','1.0.0')]);
    }
}
