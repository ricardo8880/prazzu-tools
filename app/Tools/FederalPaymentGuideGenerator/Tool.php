<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator;

use App\Core\Tools\Api\Contracts\HasApiActions;
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

use App\Tools\FederalPaymentGuideGenerator\Api\Actions\CalculateApiAction;

final class Tool implements HasApiActions, HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public function apiActions(): array
    {
        return [CalculateApiAction::class];
    }

    public const SLUG = 'gerador-darf-gps';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Gerador Inteligente de DARF/GPS',
            description: 'Oriente código, vencimento e acréscimos legais de DARF e GPS com memória de cálculo auditável.',
            category: ToolCategory::Fiscal,
            icon: 'bi-receipt-cutoff',
            routeName: 'tools.gerador-darf-gps.index',
            version: '1.1.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 50,
            featured: false,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['darf', 'gps', 'guia previdenciária', 'multa de mora', 'selic', 'código de receita'],
            capabilities: [ToolCapability::History, ToolCapability::VersionedPersistence, ToolCapability::SensitiveData, ToolCapability::Export],
            features: [
                new ToolFeature('guide_identification', 'Orientação de tipo e código da guia', ToolFeatureTier::Essential),
                new ToolFeature('due_date', 'Determinação e conferência do vencimento', ToolFeatureTier::Essential),
                new ToolFeature('late_charges', 'Cálculo transparente de multa e juros informados', ToolFeatureTier::Essential),
                new ToolFeature('calculation_memory', 'Memória completa do cálculo', ToolFeatureTier::Essential),
                new ToolFeature('favorites', 'Favoritos para cálculos recorrentes', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Histórico e reaproveitamento', ToolFeatureTier::Plus),
                new ToolFeature('professional_export', 'Exportações e relatórios', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: new ToolSensitiveDataPolicy(SensitiveDataMode::Encrypted, ['history_payload']),
        );
    }


    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: ['guide_type', 'revenue_code', 'principal', 'due_date', 'payment_date', 'selic_accumulated_percent'],
            resultFields: ['guide', 'dates', 'amounts', 'calculation', 'warnings'],
            sensitiveFields: ['principal'],
        );
    }

    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-gerador-darf-gps'; }
}
