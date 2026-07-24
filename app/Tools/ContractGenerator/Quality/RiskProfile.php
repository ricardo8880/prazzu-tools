<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Quality;

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
            toolSlug: 'gerador-de-contratos',
            nature: ToolNature::Generation,
            normativeDependency: NormativeDependency::High,
            personalDataExposure: PersonalDataExposure::Common,
            externalIntegrationDependency: ExternalIntegrationDependency::None,
            persistenceMode: PersistenceMode::History,
            processingMode: ProcessingMode::Synchronous,
            resultRisk: ResultRisk::Informational,
            updateFrequency: UpdateFrequency::Unpredictable,
            exportFormats: ['pdf', 'docx', 'json'],
        );
    }
}
