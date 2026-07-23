<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Quality;

use App\Core\Quality\Data\ToolRiskProfile;
use App\Core\Quality\Enums\ExternalIntegrationDependency;
use App\Core\Quality\Enums\NormativeDependency;
use App\Core\Quality\Enums\PersonalDataExposure;
use App\Core\Quality\Enums\PersistenceMode;
use App\Core\Quality\Enums\ProcessingMode;
use App\Core\Quality\Enums\ResultRisk;
use App\Core\Quality\Enums\ToolNature;
use App\Core\Quality\Enums\UpdateFrequency;

final class RiskProfile
{
    public static function define(): ToolRiskProfile
    {
        return new ToolRiskProfile(
            toolSlug: 'emissor-de-recibos',
            nature: ToolNature::Generation,
            normativeDependency: NormativeDependency::Low,
            personalDataExposure: PersonalDataExposure::Common,
            externalIntegrationDependency: ExternalIntegrationDependency::None,
            persistenceMode: PersistenceMode::History,
            processingMode: ProcessingMode::Synchronous,
            resultRisk: ResultRisk::Financial,
            updateFrequency: UpdateFrequency::Rare,
            exportFormats: ['pdf'],
        );
    }
}
