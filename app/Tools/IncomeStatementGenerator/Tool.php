<?php

declare(strict_types=1);

namespace App\Tools\IncomeStatementGenerator;

use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Contracts\HasToolIntegrations;
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
use App\Core\Tools\Infrastructure\Enums\SensitiveDataMode;

final class Tool implements HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(
            publishes: [],
            accepts: [],
        );
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: 'declaracao-rendimentos',
            name: 'Gerador de Declaração de Rendimentos',
            description: 'Organize valores previamente apurados em uma declaração de rendimentos revisável e pronta para assinatura.',
            category: ToolCategory::Documents,
            icon: 'bi-file-earmark-text',
            routeName: 'tools.declaracao-rendimentos.index',
            version: '1.1.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 240,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['declaração de rendimentos', 'comprovante de renda', 'imposto de renda', 'irrf', 'inss'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
                ToolCapability::SensitiveData,
            ],
            features: [
                new ToolFeature('calculate', 'Solução completa do problema', ToolFeatureTier::Essential),
                new ToolFeature('advanced_productivity', 'Produtividade avançada', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['json', 'pdf']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: new ToolSensitiveDataPolicy(SensitiveDataMode::Encrypted, ['name', 'document']),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(enabled: true, retentionDays: 365, inputFields: ['name', 'document', 'payer', 'year', 'gross', 'inss', 'irrf', 'other_deductions'], resultFields: ['gross', 'deductions', 'net'], sensitiveFields: ['name', 'document']);
    }

    public function webRoutesPath(): string
    {
        return __DIR__.'/Routes/web.php';
    }

    public function viewsPath(): string
    {
        return __DIR__.'/Resources/views';
    }

    public function viewsNamespace(): string
    {
        return 'tools-declaracao-rendimentos';
    }
}
