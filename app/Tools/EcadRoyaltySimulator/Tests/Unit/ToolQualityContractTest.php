<?php

declare(strict_types=1);

namespace App\Tools\EcadRoyaltySimulator\Tests\Unit;

use App\Core\Quality\Services\GoldenCaseSuiteValidator;
use App\Core\Quality\Services\ToolRiskClassifier;
use App\Tools\EcadRoyaltySimulator\Quality\RiskProfile;
use App\Tools\EcadRoyaltySimulator\Tests\Fixtures\GoldenCases;
use App\Tools\EcadRoyaltySimulator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolQualityContractTest extends TestCase
{
    public function test_risk_profile_and_golden_cases_are_complete(): void
    {
        self::assertSame((new Tool)->manifest()->slug, RiskProfile::define()->toolSlug);
        $suite = GoldenCases::suite();
        (new GoldenCaseSuiteValidator)->validate($suite, (new ToolRiskClassifier)->classify(RiskProfile::define()));
        foreach ($suite->cases as $case) {
            self::assertNotSame(GoldenCases::PLACEHOLDER_REFERENCE, $case->reference);
            self::assertNotEmpty($case->input);
            self::assertNotEmpty($case->expected);
        }
    }
}
