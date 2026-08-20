<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Quality;

use App\Core\Quality\Data\ToolRiskProfile;
use App\Core\Quality\Enums\ExternalIntegrationDependency;
use App\Core\Quality\Enums\NormativeDependency;
use App\Core\Quality\Enums\PersistenceMode;
use App\Core\Quality\Enums\PersonalDataExposure;
use App\Core\Quality\Enums\ProcessingMode;
use App\Core\Quality\Enums\ResultRisk;
use App\Core\Quality\Enums\ToolNature;
use App\Core\Quality\Enums\UpdateFrequency;

final class RiskProfile
{
    public static function define(): ToolRiskProfile
    {
        return new ToolRiskProfile(
            toolSlug: 'validador-de-cnpj',
            nature: ToolNature::Validation,
            normativeDependency: NormativeDependency::Low,
            personalDataExposure: PersonalDataExposure::Sensitive,
            externalIntegrationDependency: ExternalIntegrationDependency::Optional,
            persistenceMode: PersistenceMode::History,
            processingMode: ProcessingMode::Synchronous,
            resultRisk: ResultRisk::Informational,
            updateFrequency: UpdateFrequency::Unpredictable,
            exportFormats: ['csv', 'json', 'pdf'],
        );
    }
}
