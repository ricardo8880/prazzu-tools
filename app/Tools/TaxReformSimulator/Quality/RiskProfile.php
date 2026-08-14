<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Quality; use App\Core\Quality\Data\ToolRiskProfile; use App\Core\Quality\Enums\{ExternalIntegrationDependency,NormativeDependency,PersistenceMode,PersonalDataExposure,ProcessingMode,ResultRisk,ToolNature,UpdateFrequency}; final class RiskProfile { public static function define():ToolRiskProfile{return new ToolRiskProfile(toolSlug:'simulador-reforma-tributaria-consumo',nature:ToolNature::Calculation,normativeDependency:NormativeDependency::High,personalDataExposure:PersonalDataExposure::None,externalIntegrationDependency:ExternalIntegrationDependency::None,persistenceMode:PersistenceMode::Temporary,processingMode:ProcessingMode::Synchronous,resultRisk:ResultRisk::Financial,updateFrequency:UpdateFrequency::Unpredictable,exportFormats:[]);} }
