<?php

declare(strict_types=1);

namespace App\Core\Quality\Data;

use App\Core\Quality\Enums\GoldenCaseKind;
use App\Core\Quality\Enums\ToolRiskLevel;

final readonly class ToolQualityRequirements
{
    /** @param list<GoldenCaseKind> $requiredGoldenCaseKinds */
    public function __construct(
        public ToolRiskLevel $riskLevel,
        public array $requiredGoldenCaseKinds,
        public bool $requiresSpecialistReview,
        public bool $requiresNormativeMetadata,
        public bool $requiresPrivacyReview,
        public bool $requiresIntegrationResilienceTests,
        public bool $requiresQueueFailureTests,
        public bool $requiresExportTests,
        public bool $requiresBrowserTests,
    ) {}
}
