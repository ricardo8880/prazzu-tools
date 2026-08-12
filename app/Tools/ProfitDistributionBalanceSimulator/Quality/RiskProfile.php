<?php

declare(strict_types=1);
namespace App\Tools\ProfitDistributionBalanceSimulator\Quality;
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
        return new ToolRiskProfile(toolSlug:'simulador-distribuicao-lucros-balanco', nature:ToolNature::Calculation, normativeDependency:NormativeDependency::Low, personalDataExposure:PersonalDataExposure::None, externalIntegrationDependency:ExternalIntegrationDependency::None, persistenceMode:PersistenceMode::Temporary, processingMode:ProcessingMode::Synchronous, resultRisk:ResultRisk::Financial, updateFrequency:UpdateFrequency::Rare, exportFormats:['pdf','xlsx']);
    }
}
