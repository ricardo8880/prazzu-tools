<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator;

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
    public const SLUG = 'distribuicao-de-lucros';

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Calculadora de Distribuição de Lucros',
            description: 'Calcule o lucro disponível, a distribuição por participação societária e o saldo remanescente.',
            category: ToolCategory::Fiscal,
            icon: 'bi-pie-chart',
            routeName: 'tools.distribuicao-de-lucros.index',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 31,
            featured: true,
            supportsHistory: true,
            keywords: ['distribuição de lucros', 'lucros e dividendos', 'participação societária'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
            ],
            features: [
                new ToolFeature('calculate', 'Distribuição proporcional completa', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Memória do lucro disponível', ToolFeatureTier::Essential),
                new ToolFeature('partners', 'Múltiplos sócios, exercícios e cenários', ToolFeatureTier::Plus),
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
    public function viewsNamespace(): string { return 'tools-distribuicao-de-lucros'; }
}
