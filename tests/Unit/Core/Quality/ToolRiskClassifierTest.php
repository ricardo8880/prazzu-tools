<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Quality;

use App\Core\Quality\Data\ToolRiskProfile;
use App\Core\Quality\Enums\ExternalIntegrationDependency;
use App\Core\Quality\Enums\GoldenCaseKind;
use App\Core\Quality\Enums\NormativeDependency;
use App\Core\Quality\Enums\PersistenceMode;
use App\Core\Quality\Enums\PersonalDataExposure;
use App\Core\Quality\Enums\ProcessingMode;
use App\Core\Quality\Enums\ResultRisk;
use App\Core\Quality\Enums\ToolNature;
use App\Core\Quality\Enums\ToolRiskLevel;
use App\Core\Quality\Enums\UpdateFrequency;
use App\Core\Quality\Services\ToolRiskClassifier;
use PHPUnit\Framework\TestCase;

final class ToolRiskClassifierTest extends TestCase
{
    public function test_classifies_a_simple_converter_as_low_risk(): void
    {
        $requirements = (new ToolRiskClassifier)->classify(new ToolRiskProfile(
            toolSlug: 'conversor-simples',
            nature: ToolNature::Conversion,
            normativeDependency: NormativeDependency::None,
            personalDataExposure: PersonalDataExposure::None,
            externalIntegrationDependency: ExternalIntegrationDependency::None,
            persistenceMode: PersistenceMode::Temporary,
            processingMode: ProcessingMode::Synchronous,
            resultRisk: ResultRisk::Informational,
            updateFrequency: UpdateFrequency::Rare,
        ));

        $this->assertSame(ToolRiskLevel::Low, $requirements->riskLevel);
        $this->assertFalse($requirements->requiresSpecialistReview);
        $this->assertFalse($requirements->requiresBrowserTests);
        $this->assertContains(GoldenCaseKind::Typical, $requirements->requiredGoldenCaseKinds);
    }

    public function test_classifies_a_tax_tool_as_critical_and_requires_normative_evidence(): void
    {
        $requirements = (new ToolRiskClassifier)->classify(new ToolRiskProfile(
            toolSlug: 'calculadora-tributaria',
            nature: ToolNature::Calculation,
            normativeDependency: NormativeDependency::High,
            personalDataExposure: PersonalDataExposure::Common,
            externalIntegrationDependency: ExternalIntegrationDependency::Required,
            persistenceMode: PersistenceMode::History,
            processingMode: ProcessingMode::Queue,
            resultRisk: ResultRisk::Tax,
            updateFrequency: UpdateFrequency::Unpredictable,
            exportFormats: ['pdf', 'xlsx'],
        ));

        $this->assertSame(ToolRiskLevel::Critical, $requirements->riskLevel);
        $this->assertTrue($requirements->requiresSpecialistReview);
        $this->assertTrue($requirements->requiresNormativeMetadata);
        $this->assertTrue($requirements->requiresPrivacyReview);
        $this->assertTrue($requirements->requiresIntegrationResilienceTests);
        $this->assertTrue($requirements->requiresQueueFailureTests);
        $this->assertTrue($requirements->requiresExportTests);
        $this->assertTrue($requirements->requiresBrowserTests);
        $this->assertContains(GoldenCaseKind::NormativeTransition, $requirements->requiredGoldenCaseKinds);
        $this->assertContains(GoldenCaseKind::Regression, $requirements->requiredGoldenCaseKinds);
    }
}
