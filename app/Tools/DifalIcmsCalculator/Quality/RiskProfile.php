<?php

declare(strict_types=1);
namespace App\Tools\DifalIcmsCalculator\Quality;
use App\Core\Quality\Data\ToolRiskProfile; use App\Core\Quality\Enums\{ExternalIntegrationDependency,NormativeDependency,PersistenceMode,PersonalDataExposure,ProcessingMode,ResultRisk,ToolNature,UpdateFrequency};
final class RiskProfile { public static function define(): ToolRiskProfile { return new ToolRiskProfile('calculadora-difal-icms',ToolNature::Calculation,NormativeDependency::High,PersonalDataExposure::None,ExternalIntegrationDependency::None,PersistenceMode::History,ProcessingMode::Synchronous,ResultRisk::Tax,UpdateFrequency::Unpredictable,['pdf','csv']); } }
