<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Tests\Unit;

use App\Core\Quality\Services\GoldenCaseSuiteValidator;
use App\Core\Quality\Services\ToolRiskClassifier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\FederalPaymentGuideGenerator\Quality\RiskProfile;
use App\Tools\FederalPaymentGuideGenerator\Tests\Fixtures\GoldenCases;
use App\Tools\FederalPaymentGuideGenerator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolQualityContractTest extends TestCase
{
    public function test_active_tool_has_complete_approved_quality_contract(): void
    {
        $tool = new Tool;
        self::assertSame(ToolStatus::Active, $tool->manifest()->status);
        self::assertSame($tool->manifest()->slug, RiskProfile::define()->toolSlug);

        $suite = GoldenCases::suite();
        (new GoldenCaseSuiteValidator)->validate($suite, (new ToolRiskClassifier)->classify(RiskProfile::define()));

        foreach ($suite->cases as $case) {
            self::assertNotEmpty($case->input);
            self::assertNotEmpty($case->expected);
            self::assertSame(GoldenCases::NORMATIVE_VERSION, $case->normativeRuleVersion);
        }
    }
}
