<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Quality;

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
        return new ToolRiskProfile('calculadora-hora-extra', ToolNature::Calculation, NormativeDependency::High, PersonalDataExposure::None, ExternalIntegrationDependency::None, PersistenceMode::History, ProcessingMode::Synchronous, ResultRisk::Labor, UpdateFrequency::Annual, ['pdf', 'csv']);
    }
}
