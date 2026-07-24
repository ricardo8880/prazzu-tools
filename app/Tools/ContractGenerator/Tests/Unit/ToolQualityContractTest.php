<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Tests\Unit;

use App\Core\Quality\Services\GoldenCaseSuiteValidator;
use App\Core\Quality\Services\ToolRiskClassifier;
use App\Tools\ContractGenerator\Quality\RiskProfile;
use App\Tools\ContractGenerator\Tests\Fixtures\GoldenCases;
use App\Tools\ContractGenerator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolQualityContractTest extends TestCase
{
    public function test_risk_profile_matches_tool_and_requires_expected_reviews(): void
    {
        $profile = RiskProfile::define();
        $requirements = (new ToolRiskClassifier)->classify($profile);

        self::assertSame((new Tool)->manifest()->slug, $profile->toolSlug);
        self::assertTrue($requirements->requiresSpecialistReview);
        self::assertTrue($requirements->requiresPrivacyReview);
        self::assertTrue($requirements->requiresExportTests);
        self::assertTrue($requirements->requiresBrowserTests);
    }

    public function test_beta_tool_has_complete_non_placeholder_golden_cases(): void
    {
        $suite = GoldenCases::suite();
        (new GoldenCaseSuiteValidator)->validate($suite, (new ToolRiskClassifier)->classify(RiskProfile::define()));

        foreach ($suite->cases as $case) {
            self::assertNotSame(GoldenCases::PLACEHOLDER_REFERENCE, $case->reference);
            self::assertNotEmpty($case->input);
            self::assertNotEmpty($case->expected);
        }
    }
}
