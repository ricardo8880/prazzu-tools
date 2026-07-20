<?php

declare(strict_types=1);

namespace App\Core\Quality\Services;

use App\Core\Quality\Data\ToolQualityRequirements;
use App\Core\Quality\Data\ToolRiskProfile;
use App\Core\Quality\Enums\ExternalIntegrationDependency;
use App\Core\Quality\Enums\GoldenCaseKind;
use App\Core\Quality\Enums\NormativeDependency;
use App\Core\Quality\Enums\PersistenceMode;
use App\Core\Quality\Enums\PersonalDataExposure;
use App\Core\Quality\Enums\ProcessingMode;
use App\Core\Quality\Enums\ResultRisk;
use App\Core\Quality\Enums\ToolRiskLevel;
use App\Core\Quality\Enums\UpdateFrequency;

final class ToolRiskClassifier
{
    public function classify(ToolRiskProfile $profile): ToolQualityRequirements
    {
        $score = $this->score($profile);
        $riskLevel = match (true) {
            $score >= 12 => ToolRiskLevel::Critical,
            $score >= 8 => ToolRiskLevel::High,
            $score >= 4 => ToolRiskLevel::Moderate,
            default => ToolRiskLevel::Low,
        };

        $caseKinds = [
            GoldenCaseKind::Typical,
            GoldenCaseKind::Boundary,
            GoldenCaseKind::InvalidInput,
        ];

        if ($profile->resultRisk !== ResultRisk::Informational) {
            $caseKinds[] = GoldenCaseKind::Rounding;
            $caseKinds[] = GoldenCaseKind::NonApplicable;
        }

        if ($profile->normativeDependency !== NormativeDependency::None) {
            $caseKinds[] = GoldenCaseKind::NormativeTransition;
        }

        if (in_array($riskLevel, [ToolRiskLevel::High, ToolRiskLevel::Critical], true)) {
            $caseKinds[] = GoldenCaseKind::Regression;
        }

        return new ToolQualityRequirements(
            riskLevel: $riskLevel,
            requiredGoldenCaseKinds: $caseKinds,
            requiresSpecialistReview: $profile->resultRisk !== ResultRisk::Informational
                || $profile->normativeDependency === NormativeDependency::High,
            requiresNormativeMetadata: $profile->normativeDependency !== NormativeDependency::None,
            requiresPrivacyReview: $profile->personalDataExposure !== PersonalDataExposure::None,
            requiresIntegrationResilienceTests: $profile->externalIntegrationDependency !== ExternalIntegrationDependency::None,
            requiresQueueFailureTests: $profile->processingMode === ProcessingMode::Queue,
            requiresExportTests: $profile->exportFormats !== [],
            requiresBrowserTests: $riskLevel !== ToolRiskLevel::Low
                || $profile->persistenceMode !== PersistenceMode::Temporary,
        );
    }

    private function score(ToolRiskProfile $profile): int
    {
        return match ($profile->normativeDependency) {
            NormativeDependency::None => 0,
            NormativeDependency::Low => 2,
            NormativeDependency::High => 4,
        } + match ($profile->personalDataExposure) {
            PersonalDataExposure::None => 0,
            PersonalDataExposure::Common => 1,
            PersonalDataExposure::Sensitive => 3,
        } + match ($profile->externalIntegrationDependency) {
            ExternalIntegrationDependency::None => 0,
            ExternalIntegrationDependency::Optional => 1,
            ExternalIntegrationDependency::Required => 2,
        } + match ($profile->persistenceMode) {
            PersistenceMode::Temporary => 0,
            PersistenceMode::History => 1,
            PersistenceMode::Document => 2,
        } + match ($profile->processingMode) {
            ProcessingMode::Synchronous => 0,
            ProcessingMode::Queue => 1,
        } + match ($profile->resultRisk) {
            ResultRisk::Informational => 0,
            ResultRisk::Financial => 2,
            ResultRisk::Labor => 3,
            ResultRisk::Tax => 4,
        } + match ($profile->updateFrequency) {
            UpdateFrequency::Rare => 0,
            UpdateFrequency::Annual => 1,
            UpdateFrequency::Monthly => 2,
            UpdateFrequency::Unpredictable => 3,
        };
    }
}
