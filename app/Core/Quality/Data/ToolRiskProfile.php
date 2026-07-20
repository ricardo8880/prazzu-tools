<?php

declare(strict_types=1);

namespace App\Core\Quality\Data;

use App\Core\Exceptions\InvalidValue;
use App\Core\Quality\Enums\ExternalIntegrationDependency;
use App\Core\Quality\Enums\NormativeDependency;
use App\Core\Quality\Enums\PersistenceMode;
use App\Core\Quality\Enums\PersonalDataExposure;
use App\Core\Quality\Enums\ProcessingMode;
use App\Core\Quality\Enums\ResultRisk;
use App\Core\Quality\Enums\ToolNature;
use App\Core\Quality\Enums\UpdateFrequency;

final readonly class ToolRiskProfile
{
    /** @param list<string> $exportFormats */
    public function __construct(
        public string $toolSlug,
        public ToolNature $nature,
        public NormativeDependency $normativeDependency,
        public PersonalDataExposure $personalDataExposure,
        public ExternalIntegrationDependency $externalIntegrationDependency,
        public PersistenceMode $persistenceMode,
        public ProcessingMode $processingMode,
        public ResultRisk $resultRisk,
        public UpdateFrequency $updateFrequency,
        public array $exportFormats = [],
    ) {
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $toolSlug)) {
            throw new InvalidValue('O slug do perfil de risco é inválido.');
        }

        foreach ($exportFormats as $format) {
            if (! is_string($format) || ! preg_match('/^[a-z0-9]+$/', $format)) {
                throw new InvalidValue('Os formatos de exportação devem usar identificadores simples em letras minúsculas.');
            }
        }

        if (count($exportFormats) !== count(array_unique($exportFormats))) {
            throw new InvalidValue('Os formatos de exportação não podem se repetir.');
        }
    }
}
