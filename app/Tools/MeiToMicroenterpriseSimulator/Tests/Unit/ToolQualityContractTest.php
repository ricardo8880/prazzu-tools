<?php

declare(strict_types=1);

namespace App\Tools\MeiToMicroenterpriseSimulator\Tests\Unit;

use App\Core\Quality\Services\GoldenCaseSuiteValidator;
use App\Core\Quality\Services\ToolRiskClassifier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\MeiToMicroenterpriseSimulator\Quality\RiskProfile;
use App\Tools\MeiToMicroenterpriseSimulator\Tests\Fixtures\GoldenCases;
use App\Tools\MeiToMicroenterpriseSimulator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolQualityContractTest extends TestCase
{
    public function test_risk_profile_matches_the_tool_identity(): void
    {
        self::assertSame((new Tool)->manifest()->slug, RiskProfile::define()->toolSlug);
    }

    public function test_published_tool_has_complete_golden_cases(): void
    {
        $tool = new Tool;
        if ($tool->manifest()->status === ToolStatus::Draft) {
            self::addToAssertionCount(1);

            return;
        }
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
