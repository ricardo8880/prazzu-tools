<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator;

use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use App\Core\Tools\History\Data\ToolHistoryPolicy;
use App\Core\Tools\Infrastructure\Data\ToolExportPolicy;
use App\Core\Tools\Infrastructure\Data\ToolPersistencePolicy;
use App\Core\Tools\Infrastructure\Data\ToolSensitiveDataPolicy;
use App\Core\Tools\Infrastructure\Data\ToolSharingPolicy;

final class Tool implements HasHistoryPolicy, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'simulador-pro-labore-ideal';

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Simulador de Pró-Labore Ideal',
            description: 'Simule pró-labore, INSS, IRRF, valor líquido e custo empresarial com memória transparente.',
            category: ToolCategory::Fiscal,
            icon: 'bi-person-badge',
            routeName: 'tools.simulador-pro-labore-ideal.index',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 30,
            featured: true,
            supportsHistory: true,
            keywords: ['pró-labore', 'inss pró-labore', 'irrf pró-labore', 'retirada de sócio'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
            ],
            features: [
                new ToolFeature('calculate', 'Cálculo completo do pró-labore', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Memória e regras normativas', ToolFeatureTier::Essential),
                new ToolFeature('scenarios', 'Cenários anuais e comparação entre sócios', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: [],
            resultFields: ['tool_slug', 'schema_version', 'summary', 'details', 'warnings', 'next_actions', 'calculation_memory'],
            sensitiveFields: [],
        );
    }

    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-simulador-pro-labore-ideal'; }
}
