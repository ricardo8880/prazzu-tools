<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Quality;

use App\Core\Exceptions\InvalidValue;
use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Data\ToolQualityRequirements;
use App\Core\Quality\Enums\GoldenCaseKind;
use App\Core\Quality\Enums\ToolRiskLevel;
use App\Core\Quality\Services\GoldenCaseSuiteValidator;
use PHPUnit\Framework\TestCase;

final class GoldenCaseSuiteValidatorTest extends TestCase
{
    public function test_rejects_a_suite_without_a_required_case_kind(): void
    {
        $suite = new GoldenCaseSuite('ferramenta-exemplo', [
            new GoldenCase(
                identifier: 'caso.normal',
                title: 'Caso normal',
                kind: GoldenCaseKind::Typical,
                input: ['value' => 1],
                expected: ['value' => 1],
                reference: 'Referência técnica.',
            ),
        ]);

        $requirements = new ToolQualityRequirements(
            riskLevel: ToolRiskLevel::Moderate,
            requiredGoldenCaseKinds: [GoldenCaseKind::Typical, GoldenCaseKind::Boundary],
            requiresSpecialistReview: false,
            requiresNormativeMetadata: false,
            requiresPrivacyReview: false,
            requiresIntegrationResilienceTests: false,
            requiresQueueFailureTests: false,
            requiresExportTests: false,
            requiresBrowserTests: true,
        );

        $this->expectException(InvalidValue::class);

        (new GoldenCaseSuiteValidator)->validate($suite, $requirements);
    }
}
