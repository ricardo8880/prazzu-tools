<?php

declare(strict_types=1);
namespace App\Tools\OvertimeCalculator\Quality;
use App\Core\Quality\Data\ToolRiskProfile; use App\Core\Quality\Enums\{ExternalIntegrationDependency,NormativeDependency,PersistenceMode,PersonalDataExposure,ProcessingMode,ResultRisk,ToolNature,UpdateFrequency};
final class RiskProfile { public static function define(): ToolRiskProfile { return new ToolRiskProfile('calculadora-hora-extra',ToolNature::Calculation,NormativeDependency::High,PersonalDataExposure::None,ExternalIntegrationDependency::None,PersistenceMode::History,ProcessingMode::Synchronous,ResultRisk::Labor,UpdateFrequency::Annual,['pdf','csv']); } }
