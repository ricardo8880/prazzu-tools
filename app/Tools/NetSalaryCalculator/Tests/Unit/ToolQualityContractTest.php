<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Tests\Unit;

use App\Core\Quality\Services\GoldenCaseSuiteValidator;
use App\Core\Quality\Services\ToolRiskClassifier;
use App\Tools\NetSalaryCalculator\Quality\RiskProfile;
use App\Tools\NetSalaryCalculator\Tests\Fixtures\GoldenCases;
use App\Tools\NetSalaryCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolQualityContractTest extends TestCase
{
    public function test_risk_profile_matches_tool_identity_and_golden_suite_is_complete(): void
    {
        self::assertSame((new Tool)->manifest()->slug, RiskProfile::define()->toolSlug);

        $suite = GoldenCases::suite();
        $requirements = (new ToolRiskClassifier)->classify(RiskProfile::define());
        (new GoldenCaseSuiteValidator)->validate($suite, $requirements);

        foreach ($suite->cases as $case) {
            self::assertNotSame(GoldenCases::PLACEHOLDER_REFERENCE, $case->reference);
            self::assertNotEmpty($case->input);
            self::assertNotEmpty($case->expected);
        }
    }
}
